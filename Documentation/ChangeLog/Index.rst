.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _changelog:

ChangeLog
=========

1.2.1: 2015-04-28
  - Verified compatibility with row_newsevent_ 3.1.0
  - Upgraded language files for Catalan and Castillan

1.2.0: 2015-02-27
  - Possibility to use fields from roq_newsevent_ [`issue 4`_]
  - Honor time limit fields [`issue 5`_]
  - Fixed an incompatibility with older PHP versions (thanks to Marco Antonioli) [`issue 7`_]
  - Unit and functional tests
  - Refactoring of the controller
  - Tested compatibility with news_ 3.1.0 and TYPO3 7.0

1.1.1: 2015-01-21
  - Fix bug which prevent displaying two events on the same day (thanks to Carsten Hager) [`issue 6`_]

1.1.0: 2015-01-16
  - Option to specify if the first day of the week is Sunday or Monday [`issue 1`_]
  - Options to display more than one months [`issue 2`_]
  - Option to display a specific month [`issue 3`_]
  - Enabled the *Hide the pagination* option in plugin settings
  - Use translations for abbreviated days of week, instead of simply stripping the long name,
    for better internationalization.
  - Added translations for different languages.

1.0.0: 2015-01-03
  - Initial release

.. _issue 1: https://github.com/cbrunet/typo3-cb_newscal/issues/1
.. _issue 2: https://github.com/cbrunet/typo3-cb_newscal/issues/2
.. _issue 3: https://github.com/cbrunet/typo3-cb_newscal/issues/3
.. _issue 4: https://github.com/cbrunet/typo3-cb_newscal/issues/4
.. _issue 5: https://github.com/cbrunet/typo3-cb_newscal/issues/5
.. _issue 6: https://github.com/cbrunet/typo3-cb_newscal/issues/6
.. _issue 7: https://github.com/cbrunet/typo3-cb_newscal/issues/7

.. _news: http://typo3.org/extensions/repository/view/news
.. _roq_newsevent: http://typo3.org/extensions/repository/view/roq_newsevent


Acknowledgments
---------------

- Special thanks to **Jaume Presas i Puig**, who gave me a lot of good suggestions for
  improving the extension, and who tested it before the release of 1.1.0. He also provided me
  some translation files, and soem sample styles for the calendar.
  