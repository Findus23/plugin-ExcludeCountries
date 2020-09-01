# Matomo ExcludeCountries Plugin

## Description

This plugin allows you to only track visitors from specific countries (or all but specific countries) and discards data from everyone else, reducing the amound of data stored in the database.

You can verify that the plugin works by temporarily enabling [tracking debugging](https://developer.matomo.org/api-reference/tracking-api#debugging-the-tracker) and checking the log.

### Important Limitations:

- This plugin needs to check the geolocation while recieving the data. This means that every visit is geolocated twice which might cause (a small amount of) additional server load
- This plugin depends on your geolocation to be properly set up in Matomo. If the geolocation returns incorrect result, this plugin discards incorrect visitors.
