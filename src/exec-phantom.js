// Dependencies
var system = require('system')
var webpage = require('webpage')

// Arguments check
var url = system.args[1]
var token = system.args[2]
var userId = system.args[3]

if (url) {
    // Load page
    var page = webpage.create()
    phantom.clearCookies()

    if (token) {
        page.customHeaders = {
            'X-CSRF-TOKEN': token,
            'user-id': userId
        }
    }

    page.open(url, function(status) {
        if (status === 'success') {
            console.log(page.content)
        } else {
            console.log('Something Went Wrong')
        }

        phantom.exit()
    })
}
