.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _admin-manual:

Administrator Manual
====================



.. _admin-installation:

Installation
------------

This extension requires the news_ extension, as it extends it.
After installing the extension in the Extension Manager,
you should include the static template *Calendar for news*.



.. _admin-configuration:

Configuration
-------------

TypoScript constants
^^^^^^^^^^^^^^^^^^^^

plugin.tx_cbnewscal.settings.firstDayOfWeek
  0 to begin the week on Sunday, 1 to begin the week on Monday
plugin.tx_cbnewscal.settings.scrollMode
  Determine the way we navigate through calendars (when more than one calendar is displayed):

  - -1 (All but one) put the last displayed calendar in first position when
    navigating forward, and vice-versa for backwark (e.g. JAN - FEB - MAR => MAR - APR - MAY).
  - 0 (All) replace all visible calendar by the nex set of calendars
    (e.g. JAN - FEB - MAR => APR - MAY - JUN)
  - 1 (One) move one month position a time (e.g. JAN - FEB - MAR => FEB - MAR - APR)



Paths to Fluid templates are defined in TypoScript constants. However, a better way to customize templates
is to add additional paths to *plugin.tx_news.view.templateRootPaths* array.

plugin.tx_cbnewscal.view.templateRootPath
  Path to Fluid templates.
plugin.tx_cbnewscal.view.partialRootPath
  Path to Fluid partials
plugin.tx_cbnewscal.view.layoutRootPath
  Path to Fluid layouts

TypoScript setup
^^^^^^^^^^^^^^^^

page.includeCSS.tx_cbnewscal
  Default provided CSS stylesheet.


Default CSS stylesheet is included through *page.includeCSS.tx_cbnewscal*. The default provided template
includes a lot of classes; therefore, you can easily change the appearance of the displayed calendar
just by modifying the stylesheet.



Customizing templates
---------------------

The easiest way to customize the template used for rendering the calendar is
to specify an additional path in the *plugin.tx_news.view.templateRootPaths*
array. Array item 100 is the default template path for the news_ extension,
and array item 99 is the default template path for cb_newscal extension.
Therefore, items with index greater than 100 will replace defined paths. Under
the news specified path, the used template should be called *Calendar.html*
and located inside the *Newscal* folder.

As with the news_ extension, it is possible to define different templates
using the *tx_news.templateLayouts* array in PageTSConfig. Those templates
are then accessible through the plugin configuration in the backend.
In the Fluid template, the template number is given by the *{settings.templateLayout}*
variable.

It is possible to replace the used CSS stylesheet by modifying the path inside
*page.includeCSS.tx_cbnewscal* TypoScript setup.


Variables given to the template
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The following variables are accessible from the Fluid template.

settings
  The array of settings, configured in TypoScript or in the plugin flexform.
months
  The list of calendars to display.
navigation
  An array containing information for generation navigation arrows.

**months** is an array. Each item of that array is an array containing
the following variables:

month
  Month number (e.g. 1 for January, 12 for December) of the given calendar
year
  Year of the given calendar.
curmonth
  True if this month is the current month. If only one month is displayed,
  this is always true. If more than one month is displayed, this is true
  for the month related to the current demand.
weeks
  The array containing the weeks of this calendar.

**weeks** contains, for each week, an array of days. Each day
is itself an array, containing:

ts
  Timestamp of the day.
day
  Day of the month (1 to 31)
month
  Month (beause the day could belong to the previous or the next month as well).
curmonth
  True if the day belongs to the curently displayed month (useful for graying
  out days of previous or next month).
curday
  True if the day is today.
startev
  True is the day indicates the start of an event. If roq_newsevent is not
  used, this is always true.
endev
  True if the day indicates the end of an event. If roq_newsevent is not
  used, this is always true.
news
  The array of news related to this day. Empty if no news are available
  for this day.

Each item of the **news** array are *news* (or *event*) objects. 


The **navigation** array contains the following values:

numberOfMonths
  Contain the number of displayed months.
uid
  Uid of the plugin content object. Used for anchoring to the calendar
  when navigation through months.
next
  **next.month** and **next.year** contain month and year of the next
  month to navigate to. **next** is Null if no navigation is possible
  (because of time limit constraint).
prev
  **prev.month** and **prev.year** contain month and year of the previous
  month to navigate to. **prev** is Null if no navigation is possible
  (because of time limit constraint).


Internationalization
--------------------

Translations for this extension are stored in xlf files, just like other TYPO3 extensions.
However, many strings a taken directly from the news_ extension, while a few specific strings
are stored in the cb_newscal extension.

Extension is written in English, and French, Catalan,
and Castillan (Spanish) translations are provided. If you would like to provide
other translations, please create a pull request on GitHub_.

To replace default provided translations, or to provide your own translations,
simply copy the needed files that are located in `Resources/Private/Language/`
to a readable location, modify the files as you need, and provide the path
to those files in the `$GLOBALS['TYPO/_CONF_VARS']` variable. This variable can
be modified, either in the `typo3conf/AdditionalConfiguration.php` file for a local
TYPO3 installation, or in the `ext_localconf.php` file if you include them in
a custom extension.

The syntax to override translation files is like:

.. code-block:: php

  $GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['EXT:cb_newscal/Resources/Private/Language/locallang.xlf'][] = 'path/to/locallang.xlf';
  $GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['fr']['EXT:cb_newscal/Resources/Private/Language/locallang.xlf'][] = 'other/path/to/fr.locallang.xlf';

See `Xavier Perseguers blog`_ for more details about translations in TYPO3.

.. _news: http://typo3.org/extensions/repository/view/news
.. _Xavier Perseguers blog: http://xavier.perseguers.ch/tutoriels/typo3/articles/managing-localization-files.html#c962
.. _GitHub: https://github.com/cbrunet/typo3-cb_newscal