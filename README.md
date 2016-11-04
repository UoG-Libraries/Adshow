# Adshow
Information screens developed for Universities, available to all.

## About
Primarily designed for our in-house needs at the University of Gloucestershire, this system has been developed to be both flexible and scalable.  A solution to be utilised in many and varied settings insdie and outside of the HE sector.

## Setup

### app.ini
Add the `app.ini` file to the root of the project and complete the following fields: 
```
[application]
app_name = REPLACE

[database]
db_host = REPLACE
db_name = adshow
db_user = REPLACE (read & write access)
db_pwd = REPLACE
db_user_ro = REPLACE (read access)
db_pwd_ro = REPLACE
```

### Database
Create the database from the `adshow-sql-structure.sql` located in the documentation folder.

## Contribute
Open an issue to suggest improvements or report bugs.  You can also fork the project, amend the code and submit a pull request related to an issue.

## Credits
Vesion 0.1.0 Prototype created by [Paul Griffiths](https://github.com/alleycat58uk)
Version 1.0.0 done by: **The Swiss guys** ([Laura Belsanti](https://github.com/lbelsanti), [Lukas Bischof](https://github.com/lukasbischof), [Raphael Jenni](https://github.com/RaphaelJenni))
