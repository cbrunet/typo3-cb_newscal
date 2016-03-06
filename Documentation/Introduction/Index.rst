.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _introduction:

Introduction
============


.. _what-it-does:

What does it do?
----------------

This TYPO3 extension provides a *calendar* view to news_, as an alternative to *List* or *Date Menu* views.
It allows to quickly access news related to the current month. Default provided template displays
a small calendar for the month, with the list of news for each day as a popup. However, one could
easily customize the template to display the calendar as he wishes. 


Optionally, the extension can work with eventnews_ extension, and display events that
span multiple days.

.. _news: http://typo3.org/extensions/repository/view/news



.. _screenshots:

Screenshots
-----------


.. figure:: ../Images/Calfr.png
   :width: 261px
   :alt: Calendar for news

   Default calendar view.

   This shows the included default template and stylesheet for the calendar. Multi-day events are
   possible using eventnews_ extension. In this example, the calendar is localized in French.


.. figure:: ../Images/Calendrier.png
   :width: 274px
   :alt: Calendar for news

   An real-life example of calendar view.

   This a a screenshot of the cb_newscal extension used on a real website (http://op-dma.com/).
   Stylesheet was personalized to fit appearence of the website.
   The opened popup shows the link to a news related to November 30, 2014.



Upgrading
---------

To upgrade to cb_newscal 2.0.0, you should perform the following steps:

#. Desactivate cb_newscal extension;
#. Desactivate roq_newsevent extension (if used);
#. Upgrade news_ extension to the latest version;
#. Install eventnews_ extension (optional);
#. Upgrade cb_newscal extension;
#. Run the upgrade script in Install Tools (important!)

If you were using roq_newsevent, the upgrade script will automaticall convert
the previously created events to eventnews_. Do not remove roq_newsevent database
fields before performing the upgrade. After the upgrade is performed, you can
cleanup the roq_newsevent fields of the database.

.. important::
    
    Be aware that templates from cb_newscal 1.x are not compatible with cb_newscal 1.2. If you used
    custom templates in a previous version of cb_newscal, you will need to upgrade them.
    Use the provided default template as a basis for customization.

.. _eventnews: http://typo3.org/extensions/repository/view/eventnews
