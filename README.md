ez-performance-measure-bundle
=============================

Console Scripts to measure Performance of eZ public API

Installation
------------

The project is currently not available by composer thus you need to clone it into you src directory.
In the main directory of your eZ publish project :
```
git clone https://github.com/kuborgh/ez-performance-measure-bundle.git src/Kuborgh/Bundle/MeasureBundle
```

Then you can add the Bundle to your ezpublish/EzPublishKernel.php

	public function registerBundles()
	{
        ...
        $bundles[] = new \Kuborgh\Bundle\MeasureBundle\KuborghMeasureBundle();

        return $bundles;

Now you can configure the measurements in your ezpublish/config/config.yml

```
	kuborgh_measure:
    	content_type_list_measurer:
        	measurer1:
            	service: 'kuborgh_measure.measurer.locationcontent'
	        measurer2:
    	        service: 'kuborgh_measure.measurer.searchcontent'
	    content_type_single_measurer:
    	    measurer1:
        	    service: 'kuborgh_measure.measurer.contentservice'
	        measurer2:
    	        service: 'kuborgh_measure.measurer.searchservicefindsingle'
        	measurer3:
            	service: 'kuborgh_measure.measurer.searchservicefindcontentassingle'

```

Usage
-------

```
Usage:
 kb:measure:performance_single [-iter|--iterations[="..."]] [ctype]
 kb:measure:performance_list [-iter|--iterations[="..."]] [ctype]
 kb:measure:age [-ver|--versions[="..."]] [ctype]
 kb:measure:translate [ctype] [lang]

Arguments:
 ctype                 eZ Content Type
 lang                  eZ Langugage Code

Options:
 --iterations (-iter)   Amount of content objects to load and measure (default: 100)
 --versions	(-ver)		Amount of versions to add
 --show_min_max (-mm)   Show min / max values addition to avg value (default: 0)
 --help (-h)            Display this help message.
 --quiet (-q)           Do not output any message.
 --verbose (-v|vv|vvv)  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 --version (-V)         Display this application version.
 --ansi                 Force ANSI output.
 --no-ansi              Disable ANSI output.
 --no-interaction (-n)  Do not ask any interactive question.
 --shell (-s)           Launch the shell.
 --process-isolation    Launch commands from shell as a separate process.
 --env (-e)             The Environment name. (default: "dev")
 --no-debug             Switches off debug mode.
 --siteaccess           SiteAccess to use for operations. If not provided, default siteaccess will be used

```

Example to make 10 tests on lists with the "article" content type which is provided in the ez demo page.
```
php ezpublish/console kb:measure:performance_list --iterations 10 article
```


Build in measurers
------------------

The following measurer services are provided :

* ContentServiceMeasurer (kuborgh_measure.measurer.contentservice)<br>
  Measure the time to load a content object by if via ContentService::loadContent
* LocationContentServiceMeasurer (kuborgh_measure.measurer.locationconten)<br>
  First load location via SearchService::findLocations to retrieve the content id and load the content object via ContentService::loadContent
  Use combined SearchService::findLocations & ContentService::findContent (Input: Search, Output: Objects)
* SearchContent (kuborgh_measure.measurer.searchcontent)<br>
  Use SearchService::findContent (Input: Search, Output: Objects)

(SearchService::findSingle (Search, Object))

So if you want to use every measurer listed abvove you can use the following configuration:

```
kuborgh_measure:
    content_type_measurer:
        measurer1:
            service: 'kuborgh_measure.measurer.contentservice'
		measurer2:
			service: 'kuborgh_measure.measurer.locationcontent'
		measurer3:
			service: 'kuborgh_measure.measurer.searchcontent'
```

Build your own measurer
-----------------------

All Measurers need to implement the ```MeasurerInterface``` interface and available as a service.<br>
You can use the ```AbstractMeasurer``` as a basis to start from.

```
class MyMeasurer extends AbstractMeasurer
{
	/**
     * Provide a human readable name for this measurer.
     *
     * @return string
     */
    public function getName()
    {
        return "ContentService::loadContent";
    }

    /**
	 * Load the given valueObject and return the loadtime.
	 *
	 * @param ValueObject $valueObject
	 *
	 * @return float
	 */
	public function measure(ValueObject $valueObject)
	{
		// measure load call
		$start = microtime(true);

		// ... your code for loading here

		return microtime(true) - $start;
	}
}
```

Add you measurer as a service.<br>
In your bundles service.yml
```
parameters:
    mybundle.measurer.mycustom_meaasurer.class: Your\Bundle\AppBundle\Services\Measurer\MyMeasurer

services:
	mybundle.measurer.mycustom_meaasurer:
		class: %mybundle.measurer.mycustom_meaasurer.class%
```

Add it to the configuration
```
kuborgh_measure:
    content_type_measurer:
    	mycustom:
			service: 'mybundle.measurer.mycustom_meaasurer'
```

Now your custom measurer will be used if you run the command as described in the usage section!