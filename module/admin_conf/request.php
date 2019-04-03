<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
# VERSION : 5.2
# APPLICATION : eonweb for eyesofnetwork project
#
# LICENCE :
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
#########################################
*/

$request=array();

$request["templates"]="select nagios_host_template.name,nagios_host_template.description,nagios_service.description as service,nagios_command.name as command
from nagios_command,nagios_host_template,nagios_service
where nagios_service.host_template=nagios_host_template.id
and nagios_command.id=nagios_service.check_command
union
select nagios_host_template.name,nagios_host_template.description,nagios_service.description as service,nagios_command.name as command
from nagios_command,nagios_host_template,nagios_service,nagios_service_template,nagios_service_template_inheritance
where nagios_service.host_template=nagios_host_template.id
and nagios_command.id=nagios_service_template.check_command
and nagios_service_template.id=nagios_service_template_inheritance.target_template
and nagios_service.id=nagios_service_template_inheritance.source_service
order by 1,3;";

$request["services"]="select nagios_host.name,nagios_host.alias as description,nagios_service.description as service,nagios_command.name as command 
from nagios_service,nagios_host,nagios_command 
where nagios_service.host=nagios_host.id 
and nagios_service.check_command=nagios_command.id
union
select nagios_host.name,nagios_host.alias as description,nagios_service.description as service,nagios_command.name as command 
from nagios_service_template,nagios_host,nagios_command,nagios_service,nagios_service_template_inheritance 
where nagios_service.host=nagios_host.id 
and nagios_service_template.check_command=nagios_command.id 
and nagios_service.id=nagios_service_template_inheritance.source_service 
and nagios_service_template.id=nagios_service_template_inheritance.target_template
order by 1,3;";

$request["hosts"]="select nagios_host.name,nagios_host.alias,nagios_host.address,nagios_host_template.name as template
from nagios_host,nagios_host_template,nagios_host_template_inheritance
where nagios_host.id=nagios_host_template_inheritance.source_host
and nagios_host_template.id=nagios_host_template_inheritance.target_template
order by nagios_host_template.name,nagios_host.name;";

$request["hostgroups"]="select nagios_hostgroup.name as hostgroup,nagios_hostgroup.alias,nagios_host.name
from nagios_hostgroup,nagios_host,nagios_hostgroup_membership
where nagios_hostgroup.id=nagios_hostgroup_membership.hostgroup
and nagios_host.id=nagios_hostgroup_membership.host
union
select nagios_hostgroup.name as hostgroup,nagios_hostgroup.alias,nagios_host_template.name
from nagios_hostgroup,nagios_host_template,nagios_hostgroup_membership
where nagios_hostgroup.id=nagios_hostgroup_membership.hostgroup
and nagios_host_template.id=nagios_hostgroup_membership.host_template
order by 1,3;";

$request["servicegroups"]="select nagios_service_group.name as servicegroup,nagios_service_group.alias,nagios_service.description as name
from nagios_service_group,nagios_service,nagios_service_group_member
where nagios_service_group.id=nagios_service_group_member.service_group
and nagios_service.id=nagios_service_group_member.service
union
select nagios_service_group.name,nagios_service_group.alias as servicegroup,nagios_service_template.description as name
from nagios_service_group,nagios_service_template,nagios_service_group_member
where nagios_service_group.id=nagios_service_group_member.service_group
and nagios_service_template.id=nagios_service_group_member.template
order by 1,3;";

$request ["not_in_nagios"]="select hostname,description,'cacti' as type 
from cacti.host
where hostname not in (select nagios_host.address from lilac.nagios_host) 
and hostname not in (select nagios_host.name from lilac.nagios_host)
order by 1 ;";

$request ["not_in_cacti"]="select nagios_host.name,nagios_host.address,nagios_host.alias,'nagios' as type
from lilac.nagios_host
where nagios_host.name not in (select hostname from cacti.host)
and nagios_host.address not in (select hostname from cacti.host)
order by 1 ;";

$request ["hosts_unused_templates"]="SELECT nht.id as id,
nht.name as name
FROM nagios_host_template as nht
LEFT JOIN nagios_host_template_inheritance nhti ON nht.id = nhti.target_template
WHERE nhti.target_template IS NULL;";

$request ["services_unused_templates"]="SELECT nst.id as id,
nst.name as name
FROM nagios_service_template as nst
LEFT JOIN nagios_service_template_inheritance nsti ON nst.id = nsti.target_template
WHERE nsti.target_template IS NULL;";

?>
