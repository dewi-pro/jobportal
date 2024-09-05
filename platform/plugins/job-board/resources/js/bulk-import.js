class BulkImport {
    isDownloading = false

    constructor() {
        $(document)
            .on('submit', '.form-import-data', (event) => {
                this.submit(event)
            })
            .on('click', '.download-template', (event) => {
                this.download(event)
            })
    }

    submit(event) {
        event.preventDefault()

        const form = $(event.currentTarget)
        const formData = new FormData(form.get(0))
        const button = form.find('button[type=submit]')

        const $message = $('#imported-message')
        const $listing = $('#imported-listing')
        const $show = $('.show-errors')
        const failureTemplate = $('#failure-template').html()

        $('.main-form-message').addClass('hidden')
        $message.html('')
        $listing.html('')

        $httpClient.make()
            .withLoading(form)
            .withButtonLoading(button)
            .post(form.prop('action'), formData)
            .then(({ data }) => {
                Botble.showSuccess(data.message)

                $message.removeClass().addClass('alert alert-success').html(data.data.message)
                $listing.addClass('hidden').html('')
                $show.addClass('hidden')

                form.trigger('reset')
            })
            .catch(({ response: { data } }) => {
                let result = ''
                if (data.data) {
                    data.data.map((val) => {
                        result += failureTemplate
                            .replace('__row__', val.row)
                            .replace('__attribute__', val.attribute)
                            .replace('__errors__', val.errors.join(', '))
                    })
                }

                $message.removeClass().addClass('alert alert-danger').html(data.message)

                if (result) {
                    $show.removeClass('hidden')
                    $listing.removeClass('hidden').html(result)
                }
            })
            .finally(() => {
                $('.main-form-message').removeClass('hidden')
            })
    }

    download(event) {
        event.preventDefault()

        if (this.isDownloading) {
            return
        }

        const $this = $(event.currentTarget)
        const extension = $this.data('extension')
        const $content = $this.html()

        $.ajax({
            url: $this.data('url'),
            method: 'POST',
            data: { extension },
            xhrFields: {
                responseType: 'blob',
            },
            beforeSend: () => {
                $this.html($this.data('downloading'))
                $this.addClass('text-secondary')
                this.isDownloading = true
            },
            success: (data) => {
                const anchor = document.createElement('a')
                const url = window.URL.createObjectURL(data)
                anchor.href = url
                anchor.download = $this.data('filename')
                document.body.append(anchor)
                anchor.click()
                anchor.remove()
                window.URL.revokeObjectURL(url)
            },
            error: (data) => {
                Botble.handleError(data)
            },
            complete: () => {
                setTimeout(() => {
                    $this.html($content)
                    $this.removeClass('text-secondary')
                    this.isDownloading = false
                }, 500)
            },
        })
    }
}

$(() => new BulkImport())
