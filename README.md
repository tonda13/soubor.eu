# soubor.eu

Jednoduchá platforma pro sdílení samostatných článků a dokumentů psaných v Markdownu.
Každý článek je uložen jako `.md` soubor ve složce `files/` a zobrazí se na vlastní URL — bez databáze, bez přihlašování.

Projekt běží na [www.soubor.eu](https://www.soubor.eu).

## Stack

- **PHP 8.4** + [Slim 4](https://www.slimframework.com/) — routing
- **PHP-DI** — dependency injection s autowiring
- **league/commonmark** — GitHub Flavored Markdown parser (GFM)
- **highlight.js** — zvýraznění syntaxe v blocích kódu
- **marked.js** — live preview v administraci
- **Apache** — webový server

## Struktura projektu

```
files/        # Markdown články (.md, .private.md)
uploads/      # Obrázky k článkům (strukturou odpovídá files/)
src/          # PHP controllery + middleware
templates/    # PHP šablony (včetně templates/admin/)
assets/       # CSS
bin/          # Dev skripty (app, composer, admin-password)
etc/          # Konfigurace Dockeru (startup, bash aliasy, php.ini)
data/         # Runtime data — není v gitu (heslo admina)
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

## Administrace

Administrace běží na `/admin` a je chráněna heslem. Heslo se nastavuje příkazem:

```bash
bin/admin-password
```

Hash hesla je uložen v `data/password.hash` (soubor není v gitu).

V administraci lze:
- vytvářet, editovat, přejmenovávat a mazat články
- nahrávat a importovat `.md` soubory
- spravovat obrázky k článkům (upload přes drag & drop, Ctrl+V nebo výběr souboru)

## Články

Články se přidávají jako `.md` soubory do složky `files/`. Soubory s příponou `.private.md` se na indexu nezobrazí, ale jsou přístupné přes URL.

```
files/clanek.md            → localhost/clanek
files/tajny.private.md     → localhost/tajny       (skryto z indexu)
files/sekce/clanek.md      → localhost/sekce/clanek
```

## Obrázky

Obrázky se ukládají do složky `uploads/` se strukturou odpovídající článku:

```
uploads/sekce/clanek/obrazek.jpg   → /uploads/sekce/clanek/obrazek.jpg
```

V Markdownu: `![popis](/uploads/sekce/clanek/obrazek.jpg)`
