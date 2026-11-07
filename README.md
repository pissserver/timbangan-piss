# [Start Bootstrap](http://startbootstrap.com/) - [SB Admin 2](http://startbootstrap.com/template-overviews/sb-admin-2/)

[SB Admin 2](http://startbootstrap.com/template-overviews/sb-admin-2/) is an open source, admin dashboard template for [Bootstrap](http://getbootstrap.com/) created by [Start Bootstrap](http://startbootstrap.com/).

## Getting Started

To use this template, choose one of the following options to get started:

- Download the latest release on Start Bootstrap
- Fork this repository on GitHub

## Bugs and Issues

Have a bug or an issue with this template? [Open a new issue](https://github.com/IronSummitMedia/startbootstrap-sb-admin-2/issues) here on GitHub or leave a comment on the [template overview page at Start Bootstrap](http://startbootstrap.com/template-overviews/sb-admin-2/).

## Creator

Start Bootstrap was created by and is maintained by **David Miller**, Managing Parter at [Iron Summit Media Strategies](http://www.ironsummitmedia.com/).

- https://twitter.com/davidmillerskt
- https://github.com/davidtmiller

Start Bootstrap is based on the [Bootstrap](http://getbootstrap.com/) framework created by [Mark Otto](https://twitter.com/mdo) and [Jacob Thorton](https://twitter.com/fat).

## Docker

The application uses the legacy `mysql_*` PHP API, so Docker uses PHP 5.6 and MySQL 5.7 for compatibility. Both versions are end-of-life; use this setup only for local development and do not expose it directly to the internet.

1. Start the containers with `docker compose up --build -d`.
2. Open the application at [http://localhost:8082/](http://localhost:8082/). phpMyAdmin is available at [http://localhost:8083/](http://localhost:8083/).

Log in to phpMyAdmin with username `root` and leave the password empty. The database container creates an empty `db_piss` database and keeps its data in the `db_data` volume. phpMyAdmin's upload limit is 256 MB to support larger SQL imports. Import the application's database schema and data before using it; no database dump is included in this repository. Root access without a password is intended only for local development.

## Copyright and License

Copyright 2014 Iron Summit Media Strategies, LLC. Code released under the [Apache 2.0](https://github.com/IronSummitMedia/startbootstrap-sb-admin-2/blob/gh-pages/LICENSE) license.
