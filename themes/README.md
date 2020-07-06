# Themes for EON 5.3
## Installing existing theme

### 1 - Copying files
Copy all file to EON 5.3 folder
`/srv/eyesofnetwork/eonweb/themes/`

This folder contains all your themes.


### 2 - Enable theme in Thruk
You only need to create a symbolic link between the enabled theme in thruk and the thruk theme in EON folder 

`ln -s /srv/eyesofnetwork/eonweb/themes/<ThemeNameFolder>/thruk/<ThemeNameFolder>/ /etc/thruk/themes/themes-enabled/<ThemeNameFolder>`

### 3 - Restart Apache
`systemctl restart httpd`

## Create new theme
## Theme Structure
```
.
└── <ThemeNameFolder>
    ├── eonweb
    │   ├── custom.css
    │   ├── fonts
    │   └── images
    ├── lilac
    │   ├── images
    │   │   └── icons
    │   └── style
    │       ├── fonts
    │       └── custom-lilac.css
    └── thruk
        └── <ThemeNameFolder>
            ├── fonts
            ├── images
            │   ├── logos
            └── stylesheets
                └── <ThemeNameFolder>.css
```
- custom.css is the css code associated to eonweb (header, sidebar, login, settings...)
- custom-lilac.css is the css code associated to lilac (nagios conf, settings...)
- `<ThemeNameFolder>.css` is the css code associated to thruk (devices status, service, status style...)

## Overide custom.css classes

To edit login page element in custon.css classes need to be preceded by `#login` tag
To edit other elements in custom.css classes need to be preceded by `#<ThemeFOlderName>` tag


>Attention: The `<ThemeNameFolder>` of eonweb and thruk folder must be the same
>
>The thruk main css must have the `<ThemeNameFolder>` name `<ThemeNameFolder>.css`


## Define Global Theme
Connect to database

In *eonweb* database, please add **theme** key in *configs* table.

    UPDATE configs SET value = 'EONFlatLight' WHERE name = 'theme';
```
mysql -u root -p
#enter password
use eonweb
```
# EONFlatLight Showcase

![EON](https://gymtrip.fr/axians/eon1.png)
![EON](https://gymtrip.fr/axians/eon2.png)
![EON](https://gymtrip.fr/axians/eon3.png)
![EON](https://gymtrip.fr/axians/eon4.png)
![EON](https://gymtrip.fr/axians/eon5.png)
