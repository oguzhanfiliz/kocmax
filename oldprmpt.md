~/Desktop/calisma/B2B-B2C-main
025-07-17 01:56:50 /css/filament/support/support.css?v=3.3.17.0 ..................................................................................... ~ 0s
  2025-07-17 01:56:50 /css/filament/forms/forms.css?v=3.3.17.0 ......................................................................................... ~ 0s
  2025-07-17 01:56:50 /css/filament/filament/app.css?v=3.3.17.0 ........................................................................................ ~ 0s
  2025-07-17 01:56:50 /js/filament/notifications/notifications.js?v=3.3.17.0 ........................................................................... ~ 0s
  2025-07-17 01:56:50 /js/filament/support/support.js?v=3.3.17.0 ....................................................................................... ~ 0s
  2025-07-17 01:56:50 /js/filament/filament/echo.js?v=3.3.17.0 ......................................................................................... ~ 0s
  2025-07-17 01:56:50 /js/filament/filament/app.js?v=3.3.17.0 .......................................................................................... ~ 0s
  2025-07-17 01:56:50 /js/filament/tables/components/table.js?v=3.3.17.0 ............................................................................... ~ 0s
  2025-07-17 01:56:50 /favicon.ico ..................................................................................................................... ~ 0s

   WARN  [Thu Jul 17 01:56:52 2025] PHP Fatal error: Allowed memory size of 536870912 bytes exhausted (tried to allocate 262144 bytes) in vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php on line 1786.  

   WARN  [Thu Jul 17 01:56:52 2025] PHP Fatal error: Allowed memory size of 536870912 bytes exhausted (tried to allocate 262144 bytes) in Unknown on line 0.  

  2025-07-17 01:56:51 .............
halen bu şekilde hata alıyorum 

https://filamentphp.com/docs/3.x/panels/installation buradaki tüm docları inceleyerek best practice olarak devam edelim. eğer bu konuyu kısayoldan çözebilecek esnek ve ücretsiz bir plugin'i varsa filament'in ona yönel. 
https://filamentphp.com/plugins
buradan pluginlere ulaşabilirisn
Hala bellek sorunu devam ediyor. 536MB'a çıkmasına rağmen hala yetmiyor. Bu durumda problemi daha köklü çözelim. Filament'in debug modunu açıp gerçek hatayı bulalım.

Önce Laravel Debugbar'ı yükleyelim:
cd /Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main && composer require barryvdh/laravel-debugbar --dev
./composer.json has been updated
Running composer update barryvdh/laravel-debugbar
Loading composer repositories with package information
Updating dependencies
Lock file operations: 2 installs, 0 updates, 1 removal
  - Removing kalnoy/nestedset (v6.0.6)
  - Locking barryvdh/laravel-debugbar (v3.15.4)
  - Locking php-debugbar/php-debugbar (v2.1.6)
Writing lock file
Installing dependencies from lock file (including require-dev)
Package operations: 2 installs, 0 updates, 1 removal
  - Downloading php-debugbar/php-debugbar (v2.1.6)
  - Downloading barryvdh/laravel-debugbar (v3.15.4)
  - Removing kalnoy/nestedset (v6.0.6)
  - Installing php-debugbar/php-debugbar (v2.1.6): Extracting archive
  - Installing barryvdh/laravel-debugbar (v3.15.4): Extracting archive
2 package suggestions were added by new dependencies, use `composer suggest` to see details.
Generating optimized autoload files
> Illuminate\Foundation\ComposerScripts::postAutoloadDump
> @php artisan package:discover --ansi

   INFO  Discovering packages.  

  anourvalar/eloquent-serialize ............................................................................................................... DONE
  barryvdh/laravel-debugbar ................................................................................................................... DONE
  bezhansalleh/filament-shield ................................................................................................................ DONE
  blade-ui-kit/blade-heroicons ................................................................................................................ DONE
  blade-ui-kit/blade-icons .................................................................................................................... DONE
  filament/actions ............................................................................................................................ DONE
  filament/filament ........................................................................................................................... DONE
  filament/forms .............................................................................................................................. DONE
  filament/infolists .......................................................................................................................... DONE
  filament/notifications ...................................................................................................................... DONE
  filament/support ............................................................................................................................ DONE
  filament/tables ............................................................................................................................. DONE
  filament/widgets ............................................................................................................................ DONE
  guava/filament-icon-picker .................................................................................................................. DONE
  kirschbaum-development/eloquent-power-joins ................................................................................................. DONE
  laravel/sail ................................................................................................................................ DONE
  laravel/sanctum ............................................................................................................................. DONE
  laravel/tinker .............................................................................................................................. DONE
  livewire/livewire ........................................................................................................................... DONE
  mews/purifier ............................................................................................................................... DONE
  nesbot/carbon ............................................................................................................................... DONE
  nunomaduro/collision ........................................................................................................................ DONE
  nunomaduro/termwind ......................................................................................................................... DONE
  ryangjchandler/blade-capture-directive ...................................................................................................... DONE
  spatie/laravel-ignition ..................................................................................................................... DONE
  spatie/laravel-permission ................................................................................................................... DONE

> @php artisan filament:upgrade
  ⇂ public/js/filament/forms/components/color-picker.js  
  ⇂ public/js/filament/forms/components/date-time-picker.js  
  ⇂ public/js/filament/forms/components/file-upload.js  
  ⇂ public/js/filament/forms/components/key-value.js  
  ⇂ public/js/filament/forms/components/markdown-editor.js  
  ⇂ public/js/filament/forms/components/rich-editor.js  
  ⇂ public/js/filament/forms/components/select.js  
  ⇂ public/js/filament/forms/components/tags-input.js  
  ⇂ public/js/filament/forms/components/textarea.js  
  ⇂ public/js/filament/tables/components/table.js  
  ⇂ public/js/filament/widgets/components/chart.js  
  ⇂ public/js/filament/widgets/components/stats-overview/stat/chart.js  
  ⇂ public/js/filament/filament/app.js  
  ⇂ public/js/filament/filament/echo.js  
  ⇂ public/js/filament/notifications/notifications.js  
  ⇂ public/js/filament/support/support.js  
  ⇂ public/css/filament/forms/forms.css  
  ⇂ public/css/filament/support/support.css  
  ⇂ public/css/guava/filament-icon-picker/filament-icon-picker-stylesheet.css  
  ⇂ public/css/filament/filament/app.css  

   INFO  Successfully published assets!  

   INFO  Configuration cache cleared successfully.  

   INFO  Route cache cleared successfully.  

   INFO  Compiled views cleared successfully.  

   INFO  Successfully upgraded!  

102 packages you are using are looking for funding.
Use the `composer fund` command to find out more!
> @php artisan vendor:publish --tag=laravel-assets --ansi --force

   INFO  No publishable resources for tag [laravel-assets].  

No security vulnerability advisories found.
Using version ^3.15 for barryvdh/laravel-debugbar
Şimdi hata ayıklama için daha detaylı log alalım. Önce ProductResource'ta basit bir test yapalım - tüm ilişkileri geçici olarak kaldıralım:
SKU service'te sorun yok gibi görünüyor. Şimdi diğer servisleri ve Product'un boot metodundaki sonsuz döngü ihtimalini inceleyelim. Observer var mı kontrol edelim:
Searching for the following patterns in /Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main
 - ProductObserver
 - Product::observe
Sorun hala devam ediyorsa, Category-Product ilişkisinde döngüsel bir sorun olabilir. En basit şekilde test edelim ve tüm ProductResource'u sıfırdan yazalım:
I'm sorry, I couldn't complete that request.

Request failed with error: QuotaLimit