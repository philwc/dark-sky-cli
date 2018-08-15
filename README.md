# Dark Sky CLI

This is a tool to retrieve weather information from Dark Sky.

## Getting started

Clone this project somewhere, and run

```bash
composer install
./bin/weather auth
```

This will prompt you to insert your dark sky API secret.

## Forecast

You can get a forecast for any number of locations by specifying in the command:

```bash
./bin/weather forecast:location "Manchester, UK" "Liverpool, UK" "London, UK" "Manhatten, New York"
```

By default, only a summary will be shown:
<details><summary>Summary</summary>
<p>

```
Weather for Manchester, England, United Kingdom
===============================================

Wednesday, 15th, August 2018 21:12:59 (America/New_York)
--------------------------------------------------------

Currently Partly Cloudy ⛅

                                                                                                                        
 [WARNING] Heat Advisory(Wednesday, 15th, August 2018 19:35:00)                                                         
                                                                                                                        
           HEAT ADVISORY NOW IN EFFECT UNTIL 6 PM EDT FRIDAY                                                            
           HEAT INDEX VALUES Mid to upper 90s.                                                                          
           TIMING Thursday and Friday afternoon.                                                                        
           IMPACTS Extreme heat can cause illness and death among at- risk population who cannot stay cool. The heat and
           humidity may cause heat stress during outdoor exertion or extended exposure.                                 
                                                                                                                        

Weather for Liverpool, England, United Kingdom
==============================================

Wednesday, 15th, August 2018 21:12:59 (Europe/London)
-----------------------------------------------------

Currently Mostly Cloudy ☁️

Weather for London, England, United Kingdom
===========================================

Wednesday, 15th, August 2018 21:12:59 (Europe/London)
-----------------------------------------------------

Currently Light Rain ⛆

Weather for New York City, New York, United States of America
=============================================================

Wednesday, 15th, August 2018 21:12:59 (Europe/London)
-----------------------------------------------------

Currently Partly Cloudy ☁️

```

</p>
</details>

## History

You can get historic weather for any number of locations on a specified date:

```bash
./bin/weather history:location "2018-01-01 00:00:00" "Manchester, UK" "Liverpool, UK" "London, UK" "New York, New York" 
```
By default, only a summary will be shown:
<details><summary>Summary</summary>
<p>

```
Weather for Manchester, England, United Kingdom
===============================================

Monday, 1st, January 2018 00:00:00 (Europe/London)
--------------------------------------------------

Currently Partly Cloudy ☁️

Weather for Liverpool, England, United Kingdom
==============================================

Monday, 1st, January 2018 00:00:00 (Europe/London)
--------------------------------------------------

Currently Clear 🌙

Weather for London, England, United Kingdom
===========================================

Monday, 1st, January 2018 00:00:00 (Europe/London)
--------------------------------------------------

Currently Partly Cloudy ☁️

Weather for , New York, United States of America
================================================

Monday, 1st, January 2018 00:00:00 (America/New_York)
-----------------------------------------------------

Currently Clear 🌙
```

</p>
</details>

There are several options you can append to get more detailed information:

```
      --currently        Whether to show currently information
      --currentlyDetail  Whether to show currently detailed information
      --minutely         Whether to show minutely information
      --minutelyDetail   Whether to show minutely detailed information
      --hourly           Whether to show hourly information
      --hourlyDetail     Whether to show hourly detailed information
      --daily            Whether to show hourly information
      --dailyDetail      Whether to show hourly detailed information
```

You can also pass `--help` to get context specific help
