from influxdb import InfluxDBClient 
import matplotlib.pyplot as plt
import json
import pandas as pd
# using Http
client = InfluxDBClient(database='nagflux')
# client = InfluxDBClient(host='127.0.0.1', port=8086, database='dbname')
# client = InfluxDBClient(host='127.0.0.1', port=8086, username='root', password='root', database='dbname')

interfacesIn = ['SELECT difference(mean("value")) AS "ens33_in_octet-value", mean("warn") AS "ens33_in_octet-warn", mean("warn-min") AS "ens33_in_octet-warn-min", mean("warn-max") AS "ens33_in_octet-warn-max", mean("crit") AS "ens33_in_octet-crit", mean("crit-min") AS "ens33_in_octet-crit-min", mean("crit-max") AS "ens33_in_octet-crit-max" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "interfaces" AND "command" = "check_int_traffic" AND "performanceLabel" = "ens33_in_octet") AND time >= 1637563541634ms and time <= 1637591822314ms GROUP BY time(20s) fill(null);SELECT difference(mean("value")) AS "downtime" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "interfaces" AND "command" = "check_int_traffic" AND "performanceLabel" = "ens33_in_octet" AND "downtime" = "true") AND time >= 1637563541634ms and time <= 1637591822314ms GROUP BY time(20s) fill(null)', 'SELECT difference(mean("value")) AS "downtime" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "interfaces" AND "command" = "check_int_traffic" AND "performanceLabel" = "ens33_in_octet" AND "downtime" = "true") AND $timeFilter GROUP BY time($__interval) fill(null)']

interfacesOut = ['SELECT difference(mean("value")) AS "ens33_out_octet-value", mean("warn") AS "ens33_out_octet-warn", mean("warn-min") AS "ens33_out_octet-warn-min", mean("warn-max") AS "ens33_out_octet-warn-max", mean("crit") AS "ens33_out_octet-crit", mean("crit-min") AS "ens33_out_octet-crit-min", mean("crit-max") AS "ens33_out_octet-crit-max" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "interfaces" AND "command" = "check_int_traffic" AND "performanceLabel" = "ens33_out_octet") AND $timeFilter GROUP BY time($__interval) fill(null)','SELECT difference(mean("value")) AS "downtime" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "interfaces" AND "command" = "check_int_traffic" AND "performanceLabel" = "ens33_out_octet" AND "downtime" = "true") AND $timeFilter GROUP BY time($__interval) fill(null)']

ram = ['SELECT mean("value") AS "ram_used-value", mean("warn") AS "ram_used-warn", mean("warn-min") AS "ram_used-warn-min", mean("warn-max") AS "ram_used-warn-max", mean("crit") AS "ram_used-crit", mean("crit-min") AS "ram_used-crit-min", mean("crit-max") AS "ram_used-crit-max" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "memory" AND "command" = "linux_memory" AND "performanceLabel" = "ram_used") AND $timeFilter GROUP BY time($__interval) fill(null)','SELECT mean("value") AS "downtime" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "memory" AND "command" = "linux_memory" AND "performanceLabel" = "ram_used" AND "downtime" = "true") AND $timeFilter GROUP BY time($__interval) fill(null)']

swap = ['SELECT mean("value") AS "swap_used-value", mean("warn") AS "swap_used-warn", mean("warn-min") AS "swap_used-warn-min", mean("warn-max") AS "swap_used-warn-max", mean("crit") AS "swap_used-crit", mean("crit-min") AS "swap_used-crit-min", mean("crit-max") AS "swap_used-crit-max" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "memory" AND "command" = "linux_memory" AND "performanceLabel" = "swap_used") AND $timeFilter GROUP BY time($__interval) fill(null)', 'SELECT mean("value") AS "downtime" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "memory" AND "command" = "linux_memory" AND "performanceLabel" = "swap_used" AND "downtime" = "true") AND $timeFilter GROUP BY time($__interval) fill(null)']

disk = ['SELECT mean("value") AS "/-value", mean("warn") AS "/-warn", mean("warn-min") AS "/-warn-min", mean("warn-max") AS "/-warn-max", mean("crit") AS "/-crit", mean("crit-min") AS "/-crit-min", mean("crit-max") AS "/-crit-max" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "partitions" AND "command" = "check_disk" AND "performanceLabel" = "/") AND $timeFilter GROUP BY time($__interval) fill(null)', 'SELECT mean("value") AS "downtime" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "partitions" AND "command" = "check_disk" AND "performanceLabel" = "/" AND "downtime" = "true") AND $timeFilter GROUP BY time($__interval) fill(null)']

diskBoot = ['SELECT mean("value") AS "/boot-value", mean("warn") AS "/boot-warn", mean("warn-min") AS "/boot-warn-min", mean("warn-max") AS "/boot-warn-max", mean("crit") AS "/boot-crit", mean("crit-min") AS "/boot-crit-min", mean("crit-max") AS "/boot-crit-max" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "partitions" AND "command" = "check_disk" AND "performanceLabel" = "/boot") AND $timeFilter GROUP BY time($__interval) fill(null)', 'SELECT mean("value") AS "downtime" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "partitions" AND "command" = "check_disk" AND "performanceLabel" = "/boot" AND "downtime" = "true") AND $timeFilter GROUP BY time($__interval) fill(null)']

diskDevShm = ['SELECT mean("value") AS "/dev/shm-value", mean("warn") AS "/dev/shm-warn", mean("warn-min") AS "/dev/shm-warn-min", mean("warn-max") AS "/dev/shm-warn-max", mean("crit") AS "/dev/shm-crit", mean("crit-min") AS "/dev/shm-crit-min", mean("crit-max") AS "/dev/shm-crit-max" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "partitions" AND "command" = "check_disk" AND "performanceLabel" = "/dev/shm") AND $timeFilter GROUP BY time($__interval) fill(null)', 'SELECT mean("value") AS "downtime" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "partitions" AND "command" = "check_disk" AND "performanceLabel" = "/dev/shm" AND "downtime" = "true") AND $timeFilter GROUP BY time($__interval) fill(null)']

diskRun = ['SELECT mean("value") AS "/run-value", mean("warn") AS "/run-warn", mean("warn-min") AS "/run-warn-min", mean("warn-max") AS "/run-warn-max", mean("crit") AS "/run-crit", mean("crit-min") AS "/run-crit-min", mean("crit-max") AS "/run-crit-max" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "partitions" AND "command" = "check_disk" AND "performanceLabel" = "/run") AND $timeFilter GROUP BY time($__interval) fill(null)', 'SELECT mean("value") AS "downtime" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "partitions" AND "command" = "check_disk" AND "performanceLabel" = "/run" AND "downtime" = "true") AND $timeFilter GROUP BY time($__interval) fill(null)']

diskRun0 = ['SELECT mean("value") AS "/run/user/0-value", mean("warn") AS "/run/user/0-warn", mean("warn-min") AS "/run/user/0-warn-min", mean("warn-max") AS "/run/user/0-warn-max", mean("crit") AS "/run/user/0-crit", mean("crit-min") AS "/run/user/0-crit-min", mean("crit-max") AS "/run/user/0-crit-max" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "partitions" AND "command" = "check_disk" AND "performanceLabel" = "/run/user/0") AND $timeFilter GROUP BY time($__interval) fill(null)', 'SELECT mean("value") AS "downtime" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "partitions" AND "command" = "check_disk" AND "performanceLabel" = "/run/user/0" AND "downtime" = "true") AND $timeFilter GROUP BY time($__interval) fill(null)']

diskRun998 = ['SELECT mean("value") AS "/run/user/998-value", mean("warn") AS "/run/user/998-warn", mean("warn-min") AS "/run/user/998-warn-min", mean("warn-max") AS "/run/user/998-warn-max", mean("crit") AS "/run/user/998-crit", mean("crit-min") AS "/run/user/998-crit-min", mean("crit-max") AS "/run/user/998-crit-max" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "partitions" AND "command" = "check_disk" AND "performanceLabel" = "/run/user/998") AND $timeFilter GROUP BY time($__interval) fill(null)', 'SELECT mean("value") AS "downtime" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "partitions" AND "command" = "check_disk" AND "performanceLabel" = "/run/user/998" AND "downtime" = "true") AND $timeFilter GROUP BY time($__interval) fill(null)']

diskSys = ['SELECT mean("value") AS "/sys/fs/cgroup-value", mean("warn") AS "/sys/fs/cgroup-warn", mean("warn-min") AS "/sys/fs/cgroup-warn-min", mean("warn-max") AS "/sys/fs/cgroup-warn-max", mean("crit") AS "/sys/fs/cgroup-crit", mean("crit-min") AS "/sys/fs/cgroup-crit-min", mean("crit-max") AS "/sys/fs/cgroup-crit-max" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "partitions" AND "command" = "check_disk" AND "performanceLabel" = "/sys/fs/cgroup") AND $timeFilter GROUP BY time($__interval) fill(null)', 'SELECT mean("value") AS "downtime" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "partitions" AND "command" = "check_disk" AND "performanceLabel" = "/sys/fs/cgroup" AND "downtime" = "true") AND $timeFilter GROUP BY time($__interval) fill(null)']

cpu = ['SELECT mean("value") AS "cpu_prct_used-value", mean("warn") AS "cpu_prct_used-warn", mean("warn-min") AS "cpu_prct_used-warn-min", mean("warn-max") AS "cpu_prct_used-warn-max", mean("crit") AS "cpu_prct_used-crit", mean("crit-min") AS "cpu_prct_used-crit-min", mean("crit-max") AS "cpu_prct_used-crit-max" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "processor" AND "command" = "linux_cpu" AND "performanceLabel" = "cpu_prct_used") AND $timeFilter GROUP BY time($__interval) fill(null)', 'SELECT mean("value") AS "downtime" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "processor" AND "command" = "linux_cpu" AND "performanceLabel" = "cpu_prct_used" AND "downtime" = "true") AND $timeFilter GROUP BY time($__interval) fill(null)']

alivePl = ['SELECT mean("value") AS "pl-value", mean("warn") AS "pl-warn", mean("warn-min") AS "pl-warn-min", mean("warn-max") AS "pl-warn-max", mean("crit") AS "pl-crit", mean("crit-min") AS "pl-crit-min", mean("crit-max") AS "pl-crit-max" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "hostcheck" AND "command" = "check-host-alive" AND "performanceLabel" = "pl") AND $timeFilter GROUP BY time($__interval) fill(null)', 'SELECT mean("value") AS "downtime" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "hostcheck" AND "command" = "check-host-alive" AND "performanceLabel" = "pl" AND "downtime" = "true") AND $timeFilter GROUP BY time($__interval) fill(null)']

aliveRta = ['SELECT mean("value") AS "rta-value", mean("warn") AS "rta-warn", mean("warn-min") AS "rta-warn-min", mean("warn-max") AS "rta-warn-max", mean("crit") AS "rta-crit", mean("crit-min") AS "rta-crit-min", mean("crit-max") AS "rta-crit-max" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "hostcheck" AND "command" = "check-host-alive" AND "performanceLabel" = "rta") AND $timeFilter GROUP BY time($__interval) fill(null)', 'SELECT mean("value") AS "downtime" FROM "metrics" WHERE ("host" = "localhost" AND "service" = "hostcheck" AND "command" = "check-host-alive" AND "performanceLabel" = "rta" AND "downtime" = "true") AND $timeFilter GROUP BY time($__interval) fill(null)']

req = 'SELECT difference(mean("value")) AS "ens33_in_octet-value", mean("warn") AS "ens33_in_octet-warn", mean("warn-min") AS "ens33_in_octet-warn-min", mean("warn-max") AS "ens33_in_octet-warn-max", mean("crit") AS "ens33_in_octet-crit", mean("crit-min") AS "ens33_in_octet-crit-min", mean("crit-max") AS "ens33_in_octet-crit-max" FROM "metrics" WHERE ("host" = \'localhost\' AND "service" = \'interfaces\' AND "command" = \'check_int_traffic\' AND "performanceLabel" = \'ens33_in_octet\') AND time >= 1637563541634ms and time <= 1637591822314ms GROUP BY time(20s) fill(null);SELECT difference(mean("value")) AS "downtime" FROM "metrics" WHERE ("host" = \'localhost\' AND "service" = \'interfaces\' AND "command" = \'check_int_traffic\' AND "performanceLabel" = \'ens33_in_octet\' AND "downtime" = \'true\') AND time >= 1637563541634ms and time <= 1637591822314ms GROUP BY time(20s) fill(null)'

# print()
# time >= 1637569761162ms and time <= 1637580408584ms
#         1637404701000ms             1637578900000ms
 
result = client.query(req)
bebe = list(result[0].get_points())
data = json.dumps(bebe)
data = json.loads(data)
# print(list(result[0].get_points()))
df = pd.json_normalize(data)
# a = json.dumps(bebe[0])
# print(a)
# frame = pd.DataFrame.from_dict(a)
# dfItem = pd.DataFrame.from_records(a)
# print(frame)
df.time = pd.to_datetime(data["time"], format='%Y-%m-%d %H:%M:%S.%f')
df.set_index(["time"], inplace=True)
df.plot()
d = 0
plt.savefig("dada.png")
# aa = pd.DataFrame(result[0][0])
# aa.plot()
# plt.savefig("dada.png")
# print(result)

# data = [{'id': 1, 'name': {'first': 'Coleen', 'last': 'Volk'}},
#         {'name': {'given': 'Mose', 'family': 'Regner'}},
#         {'id': 2, 'name': 'Faye Raker'}]