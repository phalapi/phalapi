#-*- coding:utf-8 -*-
#gaoyiping (iam@gaoyiping.com) 2017-02-18
import json, urllib, urllib2

def PhalApiClient(host, service = None, params = None, timeout = None):
	url = host + ('' if service is None else ('?service=' + service))
	if params is not None:
		assert type(params) is dict, 'params type must be dict'
		assert params, 'params must is valid values'
		params = urllib.urlencode(params)
	request = urllib2.Request(url)
	response = urllib2.urlopen(request, data = params, timeout = timeout)
	return {'info': response.info(), 'state': response.getcode(), 'data': json.loads(response.read())}