#!/bin/bash

set -e

SYMFONY_VERSIONS=("6.4.*" "7.2.*" "8.0.*")

for i in "${!SYMFONY_VERSIONS[@]}"; do
    SYMFONY_VERSION="${SYMFONY_VERSIONS[$i]}"

    echo "============================================"
    echo "Testing with Symfony $SYMFONY_VERSION"
    echo "============================================"

    # Nettoyer et réinstaller les dépendances
    rm -rf vendor composer.lock

    # Installer avec la version Symfony spécifique
    SYMFONY_REQUIRE="$SYMFONY_VERSION" composer update --prefer-dist --no-interaction

    # Lancer les tests
    ./vendor/bin/phpunit

    echo "✅ Tests passés pour Symfony $SYMFONY_VERSION"
    echo ""
done

echo "🎉 Tous les tests sont passés !"
