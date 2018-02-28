**RESTFUL API using PHP - M12511094**
----
  >Restful web service to host weather information. |
  >Returns json data. |
  >Created API using PHP. |
  >Modified '.htaccess' and '/etc/httpd/conf/httpd.conf' file to remove '.php' from URL. |
  >Used File System in PHP. |
  >Access Control Allow Methods: POST, GET, DELETE  |
  >Added next date correction for forecast e.g. given date is 20141231 next date will be 20150101 |

* **URLS**
  <baseurl>/historical/ |
  <baseurl>/historical/:date |
  <baseurl>/forecast/:date 

* **Method:**

  `GET`,`POST`,`DELETE`
  
*  **URL Params**

   **Required:**
  <baseurl>/historical/ => NONE
  <baseurl>/historical/:date =>   `date=[integer] len 8`
  <baseurl>/forecast/:date =>   `date=[integer] len 8`

* **Success Response:  GET :  <baseurl>/historical/**
 	 * **Code:** 200 <br />
   	 **Content:** `[{"DATE":"20130101"}...........{"DATE":"20150102"}]`
	//LIST ALL DATES in jSON YYYYMMDD
* **Success Response:  GET :<baseurl>/historical/:date** 
 	 * **Code:** 200 <br />
   	 **Content:** `{"DATE":"20130102","TMAX":"29.5","TMIN":"15.0"}`
	//weather information for a perticular date - YYYYMMDD
* **Success Response:  DELETE :<baseurl>/historical/:date** 
 	 * **Code:** 200 <br />
   	 **Content:** `200:Success`
	//Delete weather information for a perticular date
* **Success Response:  GET:  <baseurl>/forecast/:date** 
 	 * **Code:** 200 <br />
   	 **Content:** `[{"DATE":"20180213","TMAX":"23.8","TMIN":"12.9"}......{"DATE":"20180219","TMAX":"31.7","TMIN":"18.8"}]`
	//Weather information for next 7 days

* **Create Response:  POST:  <baseurl>/historical/**
 	 * **Code:** 201 <br />
   	 **Content:** `{"DATE":"20130101"}`
	//Add weather information for a perticular date
 
* **Error Response:GET :<baseurl>/historical/:date**
   	 * **Code:** 404 NOT FOUND <br />
     	 **Content:** `{ "404:entry not found" }`


* **Sample Call: using curl**
 ```
curl -H "Content-Type: application/json" -d '{"DATE":"22222222","TMAX":"33","TMIN":"22"}' http://18.218.244.0/historical/ -X POST
curl -v http://18.218.244.0/historical/22222222 -X DELETE
  ```

* **Sample Call: using JS**

  ```javascript
    $.ajax({
      url: "<baseurl>/historical/",
      dataType: "json",
      type : "GET",
      success : function(r) {
         console.log(r);
      }
    });
  ```