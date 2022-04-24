# Plugin for YOURLS: Google Safe Browsing [![Listed in Awesome YOURLS!](https://img.shields.io/badge/Awesome-YOURLS-C5A3BE)](https://github.com/YOURLS/awesome-yourls/) [![Tests](https://github.com/YOURLS/google-safe-browsing/actions/workflows/tests.yml/badge.svg)](https://github.com/YOURLS/google-safe-browsing/actions/workflows/tests.yml)

## What for

Check every new URL against Google's Safe Browsing Lookup service, reject those who are identified as malware or phishing

## How to

* In `/user/plugins`, create a new folder named `google-safe-browsing`
* Drop these files in that directory
* Go to the Plugins administration page and activate the plugin 
* Follow on-screen instructions
* Have fun

## Disclaimer

Using this plugin requires you to understand Google Safe Browsing usage. In short:
* you need a Google account, to create project, create and activate a Google API key - not a 30 second task,
* you are limited to a certain amount of queries per day,
* you must understand that the service is not perfect.

[Read more](https://developers.google.com/safe-browsing/v4/get-started)
