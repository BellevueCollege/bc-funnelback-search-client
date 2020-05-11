# BC Funnelback Search Client

The Funnelback Search client allows for integration with Funnelback search.

## How To Create a Search Page
The Search Page can be created via placing a shortcode on the page of your choice. 
Note that the URL of the search page also needs to be configured on the Funnelback system as well, as the action of the search button
needs to point to the correct location, and this code is generated on the Funnelback server. 

## The Shortcode
The base shortcode is `[bcfunnelback_shortcode]`

### Attributes
* `query_peram`  
  Additional query parameter that can be added. Default is `txtQuery`
* `engine_url`  
  URL of primary Funnelback results endpoint. Defaults to production. 
* `collection`  
  Collection ID for funnelback; defaults to `bellevuecollege-search`
* `site_param`  
  Legacy- no longer used
* `localstorage_key`  
  Legacy- no longer used

