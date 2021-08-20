<?php

include_once('/srv/eyesofnetwork/cacti/lib/functions.php');
include_once('/srv/eyesofnetwork/cacti/lib/database.php');
include_once('/srv/eyesofnetwork/cacti/install/functions.php');
include_once('/srv/eyesofnetwork/cacti/include/global_constants.php');
// include_once('/srv/eyesofnetwork/cacti/include/global_languages.php');




/* get the current data source profiles */
if (db_table_exists('data_template_data_rra', false)) {
    $profiles_results = db_install_fetch_assoc("SELECT pattern, rrd_step, rrd_heartbeat, x_files_factor
        FROM (
            SELECT data_template_data_id, GROUP_CONCAT(rra_id) AS pattern
            FROM data_template_data_rra
            GROUP BY data_template_data_id
        ) AS dtdr
        INNER JOIN data_template_data AS dtd
        ON dtd.id=dtdr.data_template_data_id
        INNER JOIN data_template_rrd AS dtr
        ON dtd.id=dtr.local_data_template_rrd_id
        INNER JOIN rra AS r
        ON r.id IN(pattern)
        GROUP BY pattern, rrd_step, rrd_heartbeat, x_files_factor");
    $profiles = $profiles_results['data'];

    $i = 1;
    if (cacti_sizeof($profiles)) {
        foreach($profiles as $profile) {
            $pattern = $profile['pattern'];

            $save = array();
            $save['id'] = 0;
            $save['name']           = 'Upgrade Profile ' . $i;
            $save['hash']           = get_hash_data_source_profile($save['name']);
            $save['step']           = $profile['rrd_step'];
            $save['heartbeat']      = $profile['rrd_heartbeat'];
            $save['x_files_factor'] = $profile['x_files_factor'];

            $id = sql_save($save, 'data_source_profiles');

            $rras = explode(',', $pattern);

            foreach($rras as $r) {
                db_install_execute("INSERT INTO data_source_profiles_rra
                    (`data_source_profile_id`, `name`, `steps`, `rows`)
                    SELECT '$id' AS `data_source_profile_id`, `name`, `steps`, `rows` FROM `rra` WHERE `id`=" . $r);

                db_install_execute("REPLACE INTO data_source_profiles_cf
                    (data_source_profile_id, consolidation_function_id)
                    SELECT '$id' AS data_source_profile_id, consolidation_function_id FROM rra_cf WHERE rra_id=" . $r);
            }

            db_install_execute("UPDATE data_template_data
                SET data_source_profile_id=$id
                WHERE data_template_data.id IN(
                    SELECT data_template_data_id
                    FROM (
                        SELECT data_template_data_id, GROUP_CONCAT(rra_id) AS pattern
                        FROM data_template_data_rra
                        GROUP BY data_template_data_id
                        HAVING pattern='" . $pattern . "'
                    ) AS rs);");
        }

        $i++;
    }
}



if (db_column_exists('data_source_profiles_rra', 'timespan')) {
    $rras_results = db_install_fetch_assoc("SELECT * FROM data_source_profiles_rra");
    $rras         = $rras_results['data'];

    if (cacti_sizeof($rras)) {
        foreach($rras as $rra) {
            $interval_results = db_install_fetch_cell('SELECT step
                FROM data_source_profiles
                WHERE id = ?',
                array($rra['data_source_profile_id']));
            $interval = $interval_results['data'];

            $timespan = $rra['steps'] * $interval * $rra['rows'];

            $timespan = get_nearest_timespan($timespan);

            db_install_execute('UPDATE data_source_profiles_rra
                SET timespan = ?
                WHERE id = ?',
                array($timespan, $rra['id']));
        }
    }
}

// data_templatye_data
    // $profile_id_results = db_install_fetch_cell('SELECT id FROM data_source_profiles ORDER BY `default` DESC LIMIT 1');
    // $profile_id         = $profile_id_results['data'];
    // db_install_execute('UPDATE data_template_data SET data_source_profile_id = ' . $profile_id . ' WHERE data_source_profile_id = 0');
    // remplacé par 
    // UPDATE `data_template_data` SET `data_source_profile_id` = (SELECT `id` FROM `data_source_profiles` ORDER BY 'default' DESC LIMIT 1) WHERE `data_source_profile_id` = 0;


/* add the snmp query graph id to graph local */
    // db_install_execute("UPDATE graph_local AS gl
    //     INNER JOIN (
    //         SELECT DISTINCT local_graph_id, task_item_id
    //         FROM graph_templates_item
    //     ) AS gti
    //     ON gl.id=gti.local_graph_id
    //     INNER JOIN data_template_rrd AS dtr
    //     ON gti.task_item_id=dtr.id
    //     INNER JOIN data_template_data AS dtd
    //     ON dtr.local_data_id=dtd.local_data_id
    //     INNER JOIN data_input_fields AS dif
    //     ON dif.data_input_id=dtd.data_input_id
    //     INNER JOIN data_input_data AS did
    //     ON did.data_template_data_id=dtd.id
    //     AND did.data_input_field_id=dif.id
    //     INNER JOIN snmp_query_graph_rrd AS sqgr
    //     ON sqgr.snmp_query_graph_id=did.value
    //     SET gl.snmp_query_graph_id=did.value
    //     WHERE input_output='in'
    //     AND type_code='output_type'
    //     AND gl.snmp_query_id>0");
    // mis dans le fichier sql

// Resolve issues with bogus templates issue #1761
$snmp_queries_results = db_install_fetch_assoc('SELECT id, name FROM snmp_query ORDER BY id');
$snmp_queries = $snmp_queries_results['data'];

if (cacti_sizeof($snmp_queries)) {
    foreach($snmp_queries as $query) {
        db_execute_prepared("UPDATE graph_local AS gl
            INNER JOIN (
                SELECT graph_template_id
                FROM graph_local AS gl
                WHERE snmp_query_id = ?
                HAVING graph_template_id NOT IN (
                    SELECT graph_template_id
                    FROM snmp_query_graph
                    WHERE snmp_query_id = ?)
            ) AS rs
            ON gl.graph_template_id=rs.graph_template_id
            SET snmp_query_id=0, snmp_query_graph_id=0, snmp_index=''",
            array($query['id'], $query['id']));
    }
}

$ids_results = db_install_fetch_assoc('SELECT * FROM graph_local WHERE snmp_query_id > 0 AND snmp_query_graph_id = 0');
$ids = $ids_results['data'];
if (cacti_sizeof($ids)) {
    foreach($ids as $id) {
        $query_graph_id_results = db_install_fetch_cell('SELECT id
            FROM snmp_query_graph
            WHERE snmp_query_id = ?
            AND graph_template_id = ?',
            array($id['snmp_query_id'], $id['graph_template_id']));
        $query_graph_id = $query_graph_id_results['data'];

        if (empty($query_graph_id)) {
            db_execute_prepared('UPDATE graph_local
                SET snmp_query_id=0, snmp_query_graph_id=0, snmp_index=""
                WHERE id = ?',
                array($id['id']));
        } else {
            db_execute_prepared('UPDATE graph_local
                SET snmp_query_graph_id=?
                WHERE id = ?',
                array($query_graph_id, $id['id']));
        }
    }
}

// 
    // db_install_execute("UPDATE graph_local AS gl
    //     INNER JOIN graph_templates_item AS gti
    //     ON gti.local_graph_id = gl.id
    //     INNER JOIN data_template_rrd AS dtr
    //     ON gti.task_item_id = dtr.id
    //     INNER JOIN data_local AS dl
    //     ON dl.id = dtr.local_data_id
    //     SET gl.snmp_query_id = dl.snmp_query_id, gl.snmp_index = dl.snmp_index
    //     WHERE gl.graph_template_id IN (SELECT graph_template_id FROM snmp_query_graph)
    //     AND gl.snmp_query_id = 0");
        // mis dans le fichier sql

    // db_install_execute("UPDATE graph_local AS gl
    //     INNER JOIN (
    //         SELECT DISTINCT local_graph_id, task_item_id
    //         FROM graph_templates_item
    //     ) AS gti
    //     ON gl.id = gti.local_graph_id
    //     INNER JOIN data_template_rrd AS dtr
    //     ON gti.task_item_id = dtr.id
    //     INNER JOIN data_template_data AS dtd
    //     ON dtr.local_data_id = dtd.local_data_id
    //     INNER JOIN data_input_fields AS dif
    //     ON dif.data_input_id = dtd.data_input_id
    //     INNER JOIN data_input_data AS did
    //     ON did.data_template_data_id = dtd.id
    //     AND did.data_input_field_id = dif.id
    //     INNER JOIN snmp_query_graph_rrd AS sqgr
    //     ON sqgr.snmp_query_graph_id = did.value
    //     SET gl.snmp_query_graph_id = did.value
    //     WHERE input_output = 'in'
    //     AND type_code = 'output_type'
    //     AND gl.graph_template_id IN (SELECT graph_template_id FROM snmp_query_graph)");
        // mis dans le fichier sql

    // Fix any 'Damaged Graph' instances
    // db_install_execute("UPDATE graph_local AS gl
    //     INNER JOIN (
    //         SELECT DISTINCT local_graph_id, task_item_id
    //         FROM graph_templates_item
    //     ) AS gti
    //     ON gl.id = gti.local_graph_id
    //     INNER JOIN data_template_rrd AS dtr
    //     ON gti.task_item_id = dtr.id
    //     INNER JOIN data_template_data AS dtd
    //     ON dtr.local_data_id = dtd.local_data_id
    //     INNER JOIN data_input_fields AS dif
    //     ON dif.data_input_id = dtd.data_input_id
    //     INNER JOIN (
    //         SELECT *
    //         FROM data_input_data
    //         WHERE value RLIKE '^([0-9]+)$'
    //     ) AS did
    //     ON did.data_template_data_id = dtd.id
    //     AND did.data_input_field_id = dif.id
    //     INNER JOIN snmp_query_graph_rrd AS sqgr
    //     ON sqgr.snmp_query_graph_id = did.value
    //     SET gl.snmp_query_graph_id = did.value
    //     WHERE input_output = 'in'
    //     AND type_code = 'output_type'
    //     AND gl.snmp_query_id > 0
    //     AND gl.snmp_query_graph_id = 0");
        // mis dans le fichier sql


// 
    // $fields = "t_auto_scale_opts t_auto_scale_log t_scale_log_units t_auto_scale_rigid t_auto_padding t_base_value t_grouping t_unit_value t_unit_exponent_value t_alt_y_grid t_right_axis t_right_axis_label t_right_axis_format t_right_axis_formatter t_left_axis_formatter t_no_gridfit t_unit_length t_tab_width t_dynamic_labels t_force_rules_legend t_legend_position t_legend_direction t_image_format_id t_title t_height t_width t_upper_limit t_lower_limit t_vertical_label t_slope_mode t_auto_scale";

    // /* repair install issues */
    // $fields = explode(' ', $fields);
    // foreach($fields as $field) {
    //     db_install_execute("UPDATE graph_templates_graph SET $field='' WHERE $field IS NULL");
    //     db_install_execute("UPDATE graph_templates_graph SET $field='' WHERE $field='0'");
    // }
    // db_install_execute("UPDATE graph_templates_graph SET unit_value='' WHERE unit_value='on'");
        // mis dans le fichier sql

// 1.2.2
    // Find aggregates with orphaned items
    $aggregates_results = db_install_fetch_assoc('SELECT local_graph_id FROM aggregate_graphs');
    $aggregates = array_rekey($aggregates_results['data'], 'local_graph_id', 'local_graph_id');

    if (cacti_sizeof($aggregates)) {
        foreach($aggregates as $a) {
            $orphans_results = db_fetch_assoc_prepared('SELECT local_data_id, COUNT(DISTINCT local_graph_id) AS graphs
                    FROM graph_templates_item AS gti
                    INNER JOIN data_template_rrd AS dtr
                    ON gti.task_item_id=dtr.id
                    WHERE dtr.local_data_id IN (
                        SELECT DISTINCT local_data_id
                        FROM graph_templates_item AS gti
                        INNER JOIN data_template_rrd AS dtr
                        ON gti.task_item_id=dtr.id
                        WHERE local_data_id > 0
                        AND gti.local_graph_id = ?
                    )
                    GROUP BY dtr.local_data_id
                    HAVING graphs = 1',
                    array($a));

            $orphans = array_rekey($orphans_results, 'local_data_id', 'local_data_id');

            if (cacti_sizeof($orphans)) {
                cacti_log('Found ' . cacti_sizeof($orphans) . ' orphaned Data Source(s) in Aggregate Graph ' . $a . ' with Local Data IDs of ' . implode(', ', $orphans), false, 'UPGRADE');

                db_execute_prepared('DELETE
                    FROM graph_templates_item
                    WHERE local_graph_id = ?
                    AND task_item_id IN(
                        SELECT dtr.id
                        FROM data_template_rrd AS dtr
                        WHERE local_data_id IN (' . implode(', ', $orphans) . ')
                    )',
                    array($a));
            }
        }
    }

    
// 1.0.0
    // if (db_column_exists('graph_tree', 'sequence', false)) {
    //     /* allow sorting of trees */
    //     $trees_results = db_install_fetch_assoc('SELECT id FROM graph_tree ORDER BY name');
    //     $trees         = $trees_results['data'];
    //     if (cacti_sizeof($trees)) {
    //         foreach($trees as $sequence => $tree) {
    //             db_install_execute('UPDATE graph_tree SET sequence = ? WHERE id = ?', array($sequence+1, $tree['id']));
    //         }
    //     }
    // }
    // mis dans le fichier sql
    // SET @sequence=0;
    // UPDATE `graph_tree` SET `sequence`= @sequence:= (@sequence+1) ORDER BY `name`;



// 1.0.0
// Convert all trees to new format, but never run more than once
if (db_column_exists('graph_tree_items', 'order_key', false)) {
    $trees_result = db_install_fetch_assoc('SELECT id FROM graph_tree ORDER BY id');
    $trees = $trees_result['data'];

    if (cacti_sizeof($trees)) {
        foreach($trees as $t) {
            $tree_items_result = db_install_fetch_assoc("SELECT *
                FROM graph_tree_items
                WHERE graph_tree_id = ?
                AND order_key NOT LIKE '___000%'
                ORDER BY order_key", array($t['id']), false);
            $tree_items = $tree_items_result['data'];

            /* reset the position variable in case we run more than once */
            db_install_execute('UPDATE graph_tree_items
                SET position=0
                WHERE graph_tree_id = ?',
                array($t['id']), false);

            $prev_parent = 0;
            $prev_id     = 0;
            $position    = 0;

            if (cacti_sizeof($tree_items)) {
                foreach($tree_items AS $item) {
                    $translated_key = rtrim($item['order_key'], "0\r\n");
                    $missing_len    = strlen($translated_key) % CHARS_PER_TIER;

                    if ($missing_len > 0) {
                        $translated_key .= substr('000', 0, $missing_len);
                    }

                    $parent_key_len   = strlen($translated_key) - CHARS_PER_TIER;
                    $parent_key       = substr($translated_key, 0, $parent_key_len);

                    $parent_id_result = db_install_fetch_cell('SELECT id
                        FROM graph_tree_items
                        WHERE graph_tree_id = ?
                        AND order_key LIKE ?',
                        array($item['graph_tree_id'],'\'' . $parent_key . '\'000%'), false);

                    $parent_id = $parent_id_result['data'];

                    if (empty($parent_id)) {
                        $parent_id = 0;
                    }

                    /* get order */
                    if ($parent_id != $prev_parent) {
                        $position = 0;
                    }

                    if (!isset($pos_array[$parent_id])) {
                        $position_result = db_install_fetch_cell('SELECT MAX(position)
                            FROM graph_tree_items
                            WHERE graph_tree_id = ?
                            AND parent = ?',
                            array($item['graph_tree_id'], $parent_id), false);

                        $position = $position_result['data'] + 1;
                    } else {
                        $position = $pos_array[$parent_id] + 1;
                    }

                    $pos_array[$parent_id] = $position;

                    $postion = $position_result['data'] + 1;

                    db_install_execute('UPDATE graph_tree_items
                        SET parent = ?, position = ?
                        WHERE id = ?',
                        array($parent_id, $position,  $item['id']), false);

                    $prev_parent = $parent_id;
                }
            }

            /* get base tree items and set position */
            $tree_items_result = db_install_fetch_assoc('SELECT *
                FROM graph_tree_items
                WHERE graph_tree_id = ?
                AND order_key LIKE "___000%"
                ORDER BY order_key',
                array($t['id']), false);
            $tree_items = $tree_items_result['data'];

            $position  = 0;
            $parent_id = 0;
            if (cacti_sizeof($tree_items)) {
                foreach($tree_items as $item) {
                    db_install_execute('UPDATE graph_tree_items
                        SET parent = ?, position = ?
                        WHERE id = ?',
                        array($parent_id, $position,  $item['id']), false);

                    $position++;
                }
            }
        }
    }

    db_install_drop_column('graph_tree_items', 'order_key');
}


// 1.0.0
    // $remove_plugins = array(
    //     'aggregate',
    //     'autom8',
    //     'clog',
    //     'discovery',
    //     'domains',
    //     'dsstats',
    //     'nectar',
    //     'realtime',
    //     'rrdclean',
    //     'settings',
    //     'snmpagent',
    //     'spikekill',
    //     'superlinks',
    //     'ugroup'
    // );
    // foreach($remove_plugins as $p) {
    //     /* remove plugin */
    //     db_install_execute("DELETE FROM plugin_config WHERE directory = ?", array($p));
    //     db_install_execute("DELETE FROM plugin_realms WHERE plugin = ?", array($p));
    //     db_install_execute("DELETE FROM plugin_db_changes WHERE plugin = ?", array($p));
    //     db_install_execute("DELETE FROM plugin_hooks WHERE name = ?", array($p));
    // }
    // remplacé par 
    // DELETE FROM plugin_config WHERE directory IN ('aggregate', 'autom8', 'clog', 'discovery', 'domains', 'dsstats', 'nectar', 'realtime', 'rrdclean', 'settings', 'snmpagent', 'spikekill', 'superlinks', 'ugroup')
    
// 1.0.0
$poller_exists = db_column_exists('poller', 'processes');
if (!$poller_exists) {
    // Take the value from the settings table and translate to
    // the new Data Collector table settings

    // Ensure value falls in line with what we expect for processes
    $max_processes = read_config_option('concurrent_processes');
    if ($max_processes < 1) $max_processes = 1;
    if ($max_processes > 10) $max_processes = 10;

    // Ensure value falls in line with what we expect for threads
    $max_threads = read_config_option('max_threads');
    if ($max_threads < 1) $max_threads = 1;
    if ($max_threads > 100) $max_threads = 100;

    db_install_execute("UPDATE poller SET processes = $max_processes, threads = $max_threads");
}



// 1.1.36
// $def_locale = repair_locale(read_config_option('i18n_default_language'));
// set_config_option('i18n_default_language', $def_locale);

$users_to_update_results = db_install_fetch_assoc('SELECT *
    FROM settings_user
    WHERE name="user_language"');
$users_to_update = $users_to_update_results['data'];

if (cacti_sizeof($users_to_update)) {
    foreach($users_to_update as $user) {
        if (strpos($user['value'], '-') === false) {
            $locale = repair_locale($user['value']);

            db_install_execute('UPDATE settings_user
                SET value = ?
                WHERE user_id = ?
                AND name = ?',
                array($locale, $user['user_id'], $user['name']));
        }
    }
}


// 1.2.0
// Upgrade debug plugin to core access by removing custom realm
    $debug_id_reports = db_install_fetch_cell('SELECT id FROM plugin_config WHERE name = \'Debug\'');
    $debug_id         = $debug_id_reports['data'];

    if ($debug_id !== false && $debug_id > 0) {
        // Plugin realms are plugin_id + 100
        $debug_id += 100;
        db_execute_prepared('DELETE FROM user_auth_realm WHERE realm_id = ?', array($debug_id));
        db_execute_prepared('DELETE FROM user_auth_group_realm WHERE realm_id = ?', array($debug_id));
    }

// 1.0.0
    $userid= db_install_fetch_cell("SELECT * FROM user_auth WHERE id='1' AND username='admin'");
    if (!empty($userid['data'])) {
    	db_install_execute("REPLACE INTO `user_auth_realm` VALUES (19,1);");
    	db_install_execute("REPLACE INTO `user_auth_realm` VALUES (22,1);");
    }



// 1.0.0
// Check for the installation of the plugin architecture
	if (db_table_exists('plugin_realms')) {
		// There can be only one of these, so just update if exist
		foreach($upgrade_realms as $r) {
			$exists_results = db_install_fetch_row('SELECT *
				FROM plugin_realms
				WHERE file LIKE ?', array('%' . $r['file_pattern'] . '%'), false);

			$exists = $exists_results['data'];

			if (cacti_sizeof($exists)) {
				$old_realm = $exists['id'] + 100;

				db_execute_prepared('UPDATE IGNORE user_auth_realm
					SET realm_id = ?
					WHERE realm_id = ?',
					array($r['new_realm'], $old_realm));
			}
		}

		// There are more than one of these so update and drop
		foreach($set_drop_realms as $r) {
			$exists_results = db_install_fetch_row('SELECT *
				FROM plugin_realms
				WHERE file LIKE ?',
				array('%' . $r['file_pattern'] . '%'), false);

			$exists = $exists_results['data'];

			if (cacti_sizeof($exists)) {
				$old_realm = $exists['id'] + 100;

				db_execute_prepared('REPLACE INTO user_auth_realm (user_id, realm_id)
					SELECT user_id, "' . $r['new_realm'] . '" AS realm_id
					FROM user_auth_realm
					WHERE realm_id = ?',
					array($old_realm));

				db_execute_prepared('DELETE FROM user_auth_realm
					WHERE realm_id = ?',
					array($old_realm));
			}
		}

		// Drop realms that have been deprecated
		foreach($drop_realms as $r) {
			$exists_results = db_install_fetch_row('SELECT *
				FROM plugin_realms
				WHERE file LIKE ?',
				array('%' . $r . '%'), false);

			$exists = $exists_results['data'];

			if ($exists) {
				$old_realm = $exists['id'] + 100;

				db_execute_prepared('DELETE FROM user_auth_realm WHERE realm_id = ?', array($old_realm));
			}
		}
	}



// Delete old sql table after updating all tables
db_install_drop_table('rra_cf');
db_install_drop_table('data_template_data_rra');
db_install_drop_table('poller_output_rt');
db_install_drop_table('rra');

?>
