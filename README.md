﻿# Brain-Import-Price
Plugin for WordPress/WooCommerce for import data from CSV file


Plugin for WordPress / WooCommerce, which imports the data from the supplier price-list in CSV format in the database. It is based on a free plugin Product Importer from Vasser Labs. Unlike the original plugin, fundamentally changed the procedure for the selection of columns CSV-file to associate with the necessary fields WooCommerce.
Main features of the plugin:
- Import fields SKU, Name, Description, Product Categories.
- Added custom fields: Supplier Code, Price Supplier, Warranty.
- Import Product Vendor field in two ways - as a separate taxonomy or attribute.
- Using the automatic calculation of the regular price at import based on the supplier price and the two adjustable parameters: the exchange rate and the trade margin.
- When imported, based on the link to the product page on the supplier website takes all available download product images and descriptions.
- Automatic generation of Tags for Products based on the fields: Category and Vendor.
- Function of the random generation of a specified quantity of Products to the first page of an online store with a Sale price (based on the above margins).
- Support for plugin fields All In One SEO Pack

TestData folder consist test CSV file for verify plugin

Плагин для WordPress/WooCommerce, который импортирует данные прайс-листа поставщика в формате CSV в базу данных. Сделан на основе бесплатного плагина Product Importer от Vasser Labs. В отличие от оригинального плагина, принципиально изменен порядок выбора колонок CSV-файла для связывания с необходимыми полями WooCommerce.
Основные возможности плагина:
- импорт полей Партномер, Название, Краткое описание, Категория товара. 
- добавлены произвольные поля: Код поставщика, Цена поставщика, Гарантия.
- импорт поля Производитель товара двумя способами - как отдельная таксономия или как аттрибут.
- используется автоматический расчет цены продажи при импорте основываясь  на цене поставщика и двух настраиваемых параметрах: курсу валюты и торговой наценке.
- при импорте, основываясь на ссылке на страницу товара на сайте поставщика, происходит скачивание всех доступных изображений товара и описания.
- автоматическая генерация меток для товара, основываясь на полях: Категория и Производитель.
- функция случайной генерации указанного количества товаров для первой страницы интернет-магазина с ценой распродажи (основывается на указанной наценке).
- поддержка полей плагина All In One SEO Pack

В папке TestData находится тестовый файл CSV для проверки работоспособности плагина

== Changelog ==
== Перечень изменений ==

= 1.0.1 = 
***** Reworked the algorithm downloading images from the server vendor - errors may occur in case of problems with the server. ***** Переработан алгоритм загрузки изображений с сервера поставщика - исключены ошибки в случае проблем с сервером
***** Added option - Import only suppliers prices for the existing products - to accelerate the daily load of supplier's price. ***** Добавлена опция - Импортировать только цены поставщика для существующих товаров - для ускорения процесса ежедневной загрузки прайса поставщика

= 1.1.0 = 
***** Reworked import Product Vendor field in two ways - as a separate taxonomy or attribute. ***** Переделан импорт поля Производитель товара двумя способами - как отдельная таксономия или как аттрибут.
***** Added option - Parsing data from internet for the existing products - to accelerate the daily load of supplier's price. ***** Добавлена опция - Импортировать данные из интернета для существующих товаров - для ускорения процесса ежедневной загрузки прайса поставщика
