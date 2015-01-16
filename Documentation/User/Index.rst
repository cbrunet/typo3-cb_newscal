.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _user-manual:

Users Manual
============

Inserting Calendar plugin
-------------------------

To insert a calendar in a page, insert a *News system* plugin.

.. figure:: ../Images/InsertPlugin.png
   :width: 700px
   :alt: Insert plugin

   Choose *News system* plugin.

Under the *Plugin* tab, choose *Calendar view*.

.. figure:: ../Images/View.png
   :width: 632px
   :alt: Calendar view

   Display Calendar view.

Most of the available options are the same than options of the news_ extension.
Please refer to the `manual of news extension`_ for more details.
We only detail options that are specific for the calendar:

Month to display
  Keep this field empty to display calendar of the current month.
  If you put a date in yyyy-aa format (e.g. 2014-01), this specific month will be displayed.
  If you put a number prefixed with + or - (e.g. +1 or -2), it will offset the current month with that value.

Date field to use
  This determines which date field is used for displaying news into the calendar.

Months to display before / Months to display after
  Number of additional months to display before and after the displayed month.
  For example, if you want to display the current month and the next month, put 1 into *Months to display after*.


The plugin displays the calendar of the current month. If month and year are given in the page url,
the calendar displays specified month and year. If the user navigates through months using links for
next or previous month, the displayed month and year will be reflected in the page url.

.. important::

   If other *news* plugin are displayed inside the same page, be careful about the
   *Disable override demand* option of the other plugins. If it is unchecked, only news of the selected
   month will be displayed. Be sure to check this option if you want to display all
   the news, whether they belong to the displayed calendar or not.



.. _news: http://typo3.org/extensions/repository/view/news
.. _manual of news extension: http://docs.typo3.org/typo3cms/extensions/news/latest/Main/Configuration/Plugin/Index.html
