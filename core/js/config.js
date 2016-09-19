/**
 * @copyright Copyright (c) 2016 Joas Schilling <coding@schilljs.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * @namespace
 */
OC.AppConfig={
	/**
	 * @param {string} method
	 * @param {string} endpoint
	 * @param {object|function} [data]
	 * @param {function} [callback]
	 */
	_call: function(method, endpoint, data, callback) {
		if (_.isFunction(data)) {
			callback = data;
			data = {};
		} else {
			data = {};
		}

		$.ajax({
			type: method.toUpperCase(),
			url: OC.generateUrl('/appconfig' + endpoint),
			data: data,
			success: function(result) {
				if (_.isFunction(callback)) {
					callback(result.data);
				}
			}
		})
	},

	/**
	 * @param {string} app
	 * @param {string} key
	 * @param {string|function} defaultValue
	 * @param {function} [callback]
	 */
	getValue:function(app,key,defaultValue,callback){
		if(_.isFunction(defaultValue)){
			callback=defaultValue;
			defaultValue=null;
		}
		this._call('get', '/' + app + '/' + key, {defaultValue: defaultValue}, callback);
	},

	/**
	 * @param {string} app
	 * @param {string} key
	 * @param {string} value
	 */
	setValue:function(app,key,value){
		this._call('post', '/' + app + '/' + key, {value: value}, callback);
	},

	/**
	 * @param {function} [callback]
	 */
	getApps:function(callback){
		this._call('get', '', callback);
	},

	/**
	 * @param {string} app
	 * @param {function} [callback]
	 */
	getKeys:function(app,callback){
		this._call('get', '/' + app, callback);
	},

	/**
	 * @param {string} app
	 * @param {string} key
	 * @param {function} [callback]
	 */
	hasKey:function(app,key,callback){
		this._call('get', '/' + app + '/' + key + '/exists', callback);
	},

	/**
	 * @param {string} app
	 * @param {string} key
	 */
	deleteKey:function(app,key){
		this._call('delete', '/' + app + '/' + key);
	},

	/**
	 * @param {string} app
	 */
	deleteApp:function(app){
		this._call('delete', '/' + app);
	}
};
