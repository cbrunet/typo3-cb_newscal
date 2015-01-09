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


FirstChar
^^^^^^^^^

Returns the first char of the provided string. Used for rendering the first letter of the day of the week.

.. code-block:: html

   {namespace c=Cbrunet\CbNewscal\ViewHelpers}

   <c:firstChar>
     <f:translate id="day.{f:format.date(date: day.ts, format: 'N')}" extensionName="news"></f:translate>
   </c:firstChar>






.. _news: http://typo3.org/extensions/repository/view/news
