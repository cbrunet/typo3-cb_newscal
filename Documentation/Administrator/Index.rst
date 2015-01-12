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

Default CSS stylesheet is included through *page.includeCSS.tx_cbnewscal*. 



Customizing templates
---------------------

The easiest way to customize the template used for rendering the calendar is
to specify an additional path in the *plugin.tx_news.view.templateRootPaths*
array. Array item 100 is the default template path for the news_ extension,
and array item 99 is the default template path for cb_calnews extension.
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
calendars
  The list of calendars to display.
demand
  The news demand object for the plugin.
uid
  uid of the displayed plugin content object.


Each calendar in the *calendars* array contains the following variables:

news
  The list of news to display.
demand
  The news demand object for the current calendar.
curmonth
  A boolean value that is TRUE if the calendar is the one specified by the demand object.
  It is FALSE for calendars of the other months, that are before or after the current month.


ViewHelpers
-----------

ViewHelpers are located under the *Cbrunet\CbNewscal\ViewHelpers* namespace.

Calendar
^^^^^^^^

This ViewHelper provides an array used to iterate through weeks and days of the given month.


.. code-block:: html

   {namespace c=Cbrunet\CbNewscal\ViewHelpers}

   <c:calendar newsList="{news}" year="{demand.year}" month="{demand.month}"
               firstDayOfWeek="{settings.firstDayOfWeek}">

   </c:calendar>


Arguments
"""""""""

newsList
  The list of news to display.
year
  (optional) The year to display. If not specified, the current year is used.
month
  (optional) The month to display. If not specified, the current month is used.
firstDayOfWeek
  (optional) The first day of the week (0 for Sunday, 1 for Monday; default: 0).


Variables inside the ViewHelper
"""""""""""""""""""""""""""""""

The following variables are accessible inside the ViewHelper.

weeks
  The array of the weeks. Each item is an array of the days of the week.
weeks.*0*.ts
  Timestamp corresponding to the day.
weeks.*0*.day
  Day of the month
weeks.*0*.month
  Month for this day (1 - 12).
weeks.*0*.curmonth
  True is the day belongs to the current month, false otherwise.
weeks.*0*.news
  Array of the news related to the current day.


OffsetMonth
^^^^^^^^^^^

This ViewHelper allow to calculate month and year corresponding to *offset* months
before or after the current month. 

Arguments
"""""""""

year
  Current year.
month
  Current month.
offset
  Number of months to offset. Positive number for months after, negative number for months before.
  For instance, offset = 1 is the next month, and offset = -1 is the previous month.

Variables inside the ViewHelper
"""""""""""""""""""""""""""""""

The following variables are accessible inside the ViewHelper.

month
  Offset month.
year
  Offset year.

Example
"""""""
.. code-block:: html

   {namespace c=Cbrunet\CbNewscal\ViewHelpers}

    <c:offsetMonth month="{demand.month}" year="{demand.year}" offset="-1">
      <f:link.action arguments="{overwriteDemand:{year: year, month: month}}"
                     title="{f:translate(id: 'month.{month}', extensionName: 'news')} {year}"
                     section="c{uid}">
        Previous month
      </f:link.action>
    </c:offsetMonth>
    <c:offsetMonth month="{demand.month}" year="{demand.year}" offset="1">
      <f:link.action arguments="{overwriteDemand:{year: year, month: month}}"
                     title="{f:translate(id: 'month.{month}', extensionName: 'news')} {year}"
                     section="c{uid}">
        Next month
      </f:link.action>
    </c:offsetMonth>


Internationalization
--------------------

Translations for this extension are stored in xlf files, just like other TYPO3 extensions.
However, many strings a taken directly from the news_ extension, while a few specific strings
are stored in the cb_newscal extension.

Extension is written in English, and French translations are provided. 

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
