Summary: EyesOfNetwork Web Interface 
Name: eonweb
Version: 5.2
Release: 0.eon
Source: https://github.com/EyesOfNetworkCommunity/%{name}/archive/master.tar.gz#/%{name}-%{version}.tar.gz
Group: Applications/System
License: GPL
Requires: backup-manager, cacti, ged, ged-mysql, eon4apps, lilac, snmptt, thruk 
Requires: httpd, mariadb-server, mod_auth_eon, mod_perl
Requires: php, php-mysql, php-ldap, php-process, php-xml
Requires: nagios >= 3.0, nagios-plugins >= 1.4.0, nagvis, nagiosbp, notifier, nagios-plugins-nrpe, pnp4nagios
Requires: net-snmp

BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-root

# appliance group and users
%define eondir          /srv/eyesofnetwork
%define	datadir		%{eondir}/%{name}
%define eonconfdir	/srv/eyesofnetworkconf/%{name}
%define snmpdir		/etc/snmp
%define backupdir	/etc

%description
EONWEB is the web frontend for the EyesOfNetwork appliance : https://www.eyesofnetwork.com.

%prep
%setup -q -n %{name}-master

%build

%install
install -d -m0755 %{buildroot}%{datadir}
install -d -m0755 %{buildroot}%{eonconfdir}
install -d -m0755 %{buildroot}%{_sysconfdir}/cron.d
install -d -m0755 %{buildroot}%{_sysconfdir}/httpd/conf.d
mv ./appliance/* %{buildroot}%{eonconfdir}
rm -rf ./appliance
cp -afv ./* %{buildroot}%{datadir}
cp -afv %{buildroot}%{eonconfdir}/eonbackup %{buildroot}%{_sysconfdir}/cron.d/
cp -afv %{buildroot}%{eonconfdir}/eonwebpurge %{buildroot}%{_sysconfdir}/cron.d/
cp -afv %{buildroot}%{eonconfdir}/eonweb.conf %{buildroot}%{_sysconfdir}/httpd/conf.d/

%post
/bin/chmod 775 %{datadir}/cache
/bin/chown -R root:eyesofnetwork %{datadir}

%clean
rm -rf %{buildroot}

%files
%{datadir}
%{eonconfdir}
%config(noreplace) %{_sysconfdir}/cron.d/eonbackup
%config(noreplace) %{_sysconfdir}/cron.d/eonwebpurge
%config(noreplace) %{_sysconfdir}/httpd/conf.d/%{name}.conf

%changelog
* Sun May 13 2018 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 5.2-0.eon
- packaged for EyesOfNetwork appliance 5.2

* Wed Jan 11 2017 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 5.1-0.eon
- packaged for EyesOfNetwork appliance 5.1

* Fri Apr 08 2016 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 5.0-0.eon
- packaged for EyesOfNetwork appliance 5.0

* Fri Dec 18 2015 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 4.2-3.eon
- highcharts ie cache false fix
- deashboard events links fix

* Tue Dec 08 2015 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 4.2-2.eon
- livestatus query contact filter fix
- ged query incidents filter fix

* Wed Dec 02 2015 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 4.2-1.eon
- highcharts instead of ezgraph added
- ldap groups added
- csv import fix
- ged refresh during edit fix 

* Tue Sep 29 2015 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 4.2-0.eon
- packaged for EyesOfNetwork appliance 4.2
- new search based on thruk
- mysqli php functions 

* Tue May 20 2014 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 4.1-2.eon
- ldap special caracters fix

* Thu May 08 2014 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 4.1-1.eon
- suppress ntop and shinken fix

* Mon Jan 06 2014 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 4.1-0.eon
- packaged for EyesOfNetwork appliance 4.1
- ldap special caracters fix
- thruk host without service search fix

* Thu Jul 18 2013 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 4.0-3.eon
- SetEnvIf Cookie for Location / fix 
- ldap user creation fix 
- ldap location with "'" fix 

* Thu Jun 20 2013 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 4.0-2.eon
- new look&feel for IE fix
- ldap alphabetical search fix
- ldap single quote fix

* Fri Jun 07 2013 Michael Aubertin <michael.aubertin@gmail.com> - 4.0-1.eon
- adding new look&feel :) From Wonderful Laurent Belgrain Design. Thank's to him.
- ldap alphabetical search fix
- ldap single quote fix

* Thu Apr 25 2013 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 4.0-0.eon
- packaged for EyesOfNetwork appliance 4.0

* Wed Mar 06 2013 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 3.1-7.eon
- admin_bp based on mysql added
- tool external autocomplete added
- tool snmp community on ip based host fix

* Wed Jan 30 2013 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 3.1-6.eon
- admin_bp added
- summary and recurring downtimes links added
- advanced eonweb search added

* Tue Jan 22 2013 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 3.1-5.eon
- capacity for nagios fix
- new thruk report link fix

* Thu Sep 06 2012 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 3.1-4.eon
- panorama link added
- event browser fix

* Thu Aug 23 2012 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 3.1-3.eon
- side menus https fix
- seconds in clock pix

* Mon Jun 18 2012 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 3.1-2.eon
- advanced notifications added
- clock in header added

* Fri Apr 06 2012 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 3.1-1.eon
- thruk reports interface link added
- event browser optimizations added
- cookie domain added
- safari fix

* Tue Mar 13 2012 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 3.1-0.eon
- packaged for EyesOfNetwork appliance 3.1

* Tue Feb 28 2012 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 3.0-2.eon
- event browser based on mysql queries fix

* Wed Nov 23 2011 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 3.0-1.eon
- login case fix

* Sun Apr 10 2011 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 3.0-0.eon
- packaged for EyesOfNetwork appliance 3.0
- change password page added
- mod_perl dependency added
- downtime scheduling added
- problems thruk view added
- thruk event log view added
- cacti hostname type for synhronization added
- ldap extended search fix

* Mon Mar 14 2011 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 2.2-3.eon
- limited user dashboard fix

* Fri Feb 18 2011 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 2.2-2.eon
- ged reports with sql requests
- nagios url and cgi in variable

* Sun Dec 05 2010 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 2.2-1.eon
- ged trigram added 
- default language fix

* Wed Jul 28 2010 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 2.2-0.eon
- packaged for EyesOfNetwork appliance 2.2
- setenvif instead of setenv in apache configuration added
- request services in admin_conf added
- owned and not owned filters in events added
- error messages in events when network/http problems added
- contacts notification commands creation added
- fop check installation link added
- max csv upload size 20480 fix
- users files delete fix
- contacts and contactgroups creation fix
- contacts and contactgroups delete fix
- nagiosbp links fix
- weathermap links fix
- generate report.doc for each users fix
- ldap "," in cn fix
- ldap password encryption

* Wed May 26 2010 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 2.1-1.eon
- hosts and templates links in nagios configuration added
- snmp v3 with "-l authpriv" added
- ged history without 2 ack time selections fix
- users and contacts mail fix
- login page ie6 fix

* Tue Feb 16 2010 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 2.1-0.eon
- apache setenv added
- nagios configuration reports added
- host template display for nagios to cacti importer added 
- cacti snmp community in importer added
- jquery 1.4.2 update
- users and contacts mail fix
- ged history duration fix
- autocomplete on report_fop fix 
- ged type 1 events sources fix 
- gedmysql.cfg fix 
- import to cacti space fix
- refresh with _blank links fix

* Fri Jul 17 2009 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 2.0-1.eon
- updates for lilac calls
- new header
- hosts,hostgroups,servicegroups autocomplete

* Fri Jul 10 2009 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 2.0-0.eon
- packaged for EyesOfNetwork appliance 2.0
- new look with navigation bar
- jquery 1.3.2
- jquery-ui 1.7.1
- lilac database rights
- updates for ged 1.2-2
- new header
- hosts,hostgroups,servicegroups autocomplete

* Mon Feb 23 2009 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 1.2-0.eon
- groups, users and ldap_users order by name
- ndo2db process management added
- ged comment with acknowledge functionality added
- ged process management fixed
- ged delete in history queue activated
- nagios hostgroups and servicesgroups links added
- nagios downtimes link renamed

* Mon Dec 22 2008 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 1.1-0.eon
- no more jpgraph welcome to eZcomponents
- french and english languages for titles
- crons for purge and backup
- new ged reports

* Mon Sep 08 2008 Jean-Philippe Levy <jeanphilippe.levy@gmail.com> - 1.0-0.eon
- packaged for EyesOfNetwork appliance
