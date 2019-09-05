[![Codacy Badge](https://api.codacy.com/project/badge/Grade/1d094996459041c08501068fd5df5310)](https://www.codacy.com/app/ZinebElam/Projet_1_ASI_D21.1?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=omarreguadi/Projet_1_ASI_D21.1&amp;utm_campaign=Badge_Grade)

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

What things you need to install the software and how to install them?

- [Docker CE](https://www.docker.com/community-edition)
- [Docker Compose](https://docs.docker.com/compose/install)

### Install

- (optional) Create your `docker-compose.override.yml` file

```bash
cp docker-compose.override.yml.dist docker-compose.override.yml
```
> Notice : Check the file content. If other containers use the same ports, change yours.

#### Init

```bash
cp .env.dist .env
docker-compose up -d
docker-compose exec --user=application web bash
```
# Projet_1_ASI_D21.1

