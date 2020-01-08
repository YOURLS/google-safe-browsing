Plugin for YOURLS 1.7+: Google Safe Browsing

# Dude

**This plugin uses Google's Safe Browsing API from 2017, version 3. As of writing, the V3 version of GSB has been deprecated. This plugin needs some good will and a pull request to be updated :)**

# What for

Check every new URL against Google's Safe Browsing Lookup service, reject those who are identified as malware or phishing

# How to

* In `/user/plugins`, create a new folder named `google-safe-browsing`
* Drop these files in that directory
* Go to the Plugins administration page and activate the plugin 
* Follow on-screen instructions
* Have fun

# Disclaimer

Using this plugin requires you to understand Google's Safe Browsing TOS. In short:
* you need a Google account
* you are limited to a certain amount of queries per day (10,000 as of writing this)
* you must understand that the service is not perfect.

[Read more](https://developers.google.com/safe-browsing/lookup_guide#AcceptableUsage)
