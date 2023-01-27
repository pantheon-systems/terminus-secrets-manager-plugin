#!/usr/bin/env bash

VCS_REF=$(git rev-parse --short HEAD)
DATE_TAG=$(TZ=UTC date +%Y-%m-%d_%H.%M)
SCRIPT=$(readlink -f "$0")
SCRIPT_PATH=$(dirname "${SCRIPT}")
ROOT_DIR=$(dirname "${SCRIPT_PATH}")
VERSION=$(cat .version)
VERSION_SAFE="${VERSION//./}"
PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;")
SITENAME="${VERSION_SAFE}-php${PHP_VERSION//./}-${VCS_REF}"


terminus site:delete "${SITENAME}" --yes --quiet &> /dev/null

echo "===================================================="
echo "Root Dir: ${ROOT_DIR}"
echo "Version: ${VERSION_SAFE}"
echo "Testing Site: ${SITENAME}"
echo "Terminus org: ${TERMINUS_ORG}"
echo "Terminus version: ${TERMINUS_VERSION}"
echo "===================================================="

echo "Installing Plugin: "
terminus self:plugin:install "${ROOT_DIR}"
echo "===================================================="

echo "Creating Site: ${SITENAME}"
## If exists is empty, create the site
terminus site:create "${SITENAME}" "${SITENAME}" drupal-composer-managed --org="${TERMINUS_ORG}"
echo "===================================================="

wait 30

echo "Installing Site: ${SITENAME}"
## Wipe the site Database and install basic umami
terminus drush "${SITENAME}.dev" -- \
     site:install --account-name=admin \
       --site-name="${SITENAME}"  \
       --locale=en --yes demo_umami
echo "===================================================="

#echo "Setting Connection: ${SITENAME}"
### set the connection of the site to GIT mode
#terminus connection:set "${SITENAME}.dev" git
#echo "===================================================="

## set the site's plan to basic paid plan
## terminus plan:set $SITENAME plan-basic_small-contract-annual-1
## export the URL for the dev environment
#export SITE_DOMAIN_NAME=$(terminus env:info "${SITENAME}.dev" --format=json --field=domain)
#echo "Loading Homepage: ${SITE_DEV}"
## curl the page once to make sure you initialize all the database tables
#curl "https://${SITE_DOMAIN_NAME}" &> /dev/null
#echo "===================================================="

terminus secret:set "${SITENAME}" "SUPER_TEST_SECRET" "${DATE_TAG}-value" --scope=user,ic
echo "Simple secret set : ${SITENAME}"
RETURN_VALUE=$(terminus secret:list "${SITENAME}" --format=json | jq -r '.[] | select(.name=="SUPER_TEST_SECRET") | .value' )
echo "Secret value retrieved: ${RETURN_VALUE} // ${DATE_TAG}"
test "${RETURN_VALUE}" == "${DATE_TAG}-value" || exit 1
echo "===================================================="

## TODO:
## 1. set a file secret/check value
## 2. set a web secret/check value with API call

echo "Deleting test site: ${SITENAME}"
terminus site:delete "${SITENAME}" --yes --quiet
