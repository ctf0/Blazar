// Arguments
const args = process.argv
let path = args[2]
let url = args[3]
let token = args[4]
let userId = args[5]

const puppeteer = require(path)

;(async () => {
    let browser
    let page
    let html

    try {
        browser = await puppeteer.launch({
            ignoreHTTPSErrors: true
        })

        page = await browser.newPage()

        if (token) {
            page.setExtraHTTPHeaders({
                'X-CSRF-TOKEN': token,
                'user-id': userId
            })
        }

        await page.goto(url, {waitUntil: 'networkidle0'})

        html = await page.content()
        console.log(html)

        await browser.close()

    } catch (exception) {
        if (browser) {
            await browser.close()
        }

        console.error(`Something Went Wrong: ${exception}`)

        process.exit(1)
    }
})()
