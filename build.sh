#!/bin/bash
rm pkg_siteplanner.zip
rm packages/com_siteplan.zip
rm packages/plg_content_siteplan.zip
rm packages/plg_k2_siteplan.zip
rm packages/joomla_sitemap_planner.zip

cd com_siteplan
zip -r ../packages/com_siteplan.zip * -x "*.DS_Store*"
cd ../plg_content_siteplan
zip -r ../packages/plg_content_siteplan.zip * -x "*.DS_Store*"
cd ../plg_k2_siteplan
zip -r ../packages/plg_k2_siteplan.zip * -x "*.DS_Store*"
cd ../joomla_sitemap_planner
zip -r ../packages/joomla_sitemap_planner.zip * -x "*.DS_Store*"

cd ../
zip -r pkg_siteplanner.zip packages/* pkg_siteplanner.xml -x "*.DS_Store*,build.sh"
