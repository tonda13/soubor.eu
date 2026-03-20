# soubor.eu

Jednoduchá platforma pro sdílení samostatných článků a dokumentů psaných v Markdownu.
Každý článek je uložen jako `.md` soubor ve složce `files/` a zobrazí se na vlastní URL — bez databáze, bez přihlašování.

Projekt běží na [www.soubor.eu](https://www.soubor.eu).

## Stack

- **PHP 8.4** + [Slim 4](https://www.slimframework.com/) — routing
- **PHP-DI** — dependency injection s autowiring
- **league/commonmark** — GitHub Flavored Markdown parser
- **highlight.js** — zvýraznění syntaxe v blocích kódu
- **Apache** — webový server

## Struktura projektu

```
files/        # Markdown články (.md, .private.md)
src/          # PHP controllery
templates/    # PHP šablony
assets/       # CSS
bin/          # Dev skripty (app, composer)
etc/          # Konfigurace Dockeru (startup, bash aliasy)
```

## Lokální vývoj

### Požadavky

- Docker
- Docker Compose

### Spuštění

```bash
# Sestavení a spuštění kontejneru
docker compose up --build

# Aplikace běží na http://localhost
```

### Instalace závislostí

```bash
bin/composer install
```

### Přihlášení do kontejneru

```bash
# jako www-data (výchozí)
bin/app

# jako root
bin/app root
```

## Články

Články se přidávají jako `.md` soubory do složky `files/`. Soubory s příponou `.private.md` se na indexu nezobrazí, ale jsou přístupné přes URL.

```
files/clanek.md          → localhost/clanek
files/tajny.private.md   → localhost/tajny  (skryto z indexu)
files/sekce/clanek.md    → localhost/sekce/clanek
```
