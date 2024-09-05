class PluginManagement {
    init() {
        $(document).on('click', '.btn-trigger-remove-plugin', (event) => {
            event.preventDefault()

            $('#confirm-remove-plugin-button').data('url', $(event.currentTarget).data('url'))
            $('#remove-plugin-modal').modal('show')
        })

        $(document).on('click', '#confirm-remove-plugin-button', (event) => {
            event.preventDefault()

            const _self = $(event.currentTarget)

            $httpClient
                .make()
                .withButtonLoading(_self)
                .delete(_self.data('url'))
                .then(({ data }) => {
                    Botble.showSuccess(data.message)
                    window.location.reload()
                })
                .finally(() => $('#remove-plugin-modal').modal('hide'))
        })

        $(document).on('click', '.btn-trigger-update-plugin', (event) => {
            event.preventDefault()

            const _self = $(event.currentTarget)
            const url = _self.data('update-url')

            _self.prop('disabled', true)

            $httpClient
                .make()
                .withButtonLoading(_self)
                .post(url)
                .then(({ data }) => {
                    Botble.showSuccess(data.message)

                    setTimeout(() => window.location.reload(), 2000)
                })
                .finally(() => _self.prop('disabled', false))
        })

        $(document).on('click', '.btn-trigger-change-status', async (event) => {
            event.preventDefault()

            const _self = $(event.currentTarget)

            const pluginName = _self.data('plugin')

            const changeStatusUrl = _self.data('change-status-url')

            if (_self.data('status') === 1) {
                Botble.showButtonLoading(_self)
                await this.activateOrDeactivatePlugin(changeStatusUrl)
                Botble.hideButtonLoading(_self)
                return
            }

            $httpClient
                .makeWithoutErrorHandler()
                .withButtonLoading(_self)
                .post(_self.data('check-requirement-url'))
                .then(() => this.activateOrDeactivatePlugin(changeStatusUrl))
                .catch((e) => {
                    const { data, message } = e.response.data

                    if (data && data.existing_plugins_on_marketplace) {
                        const $modal = $('#confirm-install-plugin-modal')
                        $modal.find('.modal-body #requirement-message').html(message)
                        $modal.find('input[name="plugin_name"]').val(pluginName)
                        $modal.find('input[name="ids"]').val(data.existing_plugins_on_marketplace)
                        $modal.modal('show')

                        return
                    }

                    Botble.showError(message)
                })
        })

        $(document).on('keyup', 'input[type="search"][name="search"]', (event) => {
            event.preventDefault()

            const search = $(event.currentTarget).val().toLowerCase()

            $('.plugin-item').each((index, element) => {
                const $element = $(element)
                const plugin = $element.data('plugin')

                const name = plugin.name.toLowerCase()
                const description = plugin.description.toLowerCase()

                if (name.includes(search) || description.includes(search)) {
                    $element.show()
                } else {
                    $element.hide()
                }
            })

            if ($('.plugin-item:visible').length === 0) {
                $('.empty').show()
            } else {
                $('.empty').hide()
            }
        })

        if ($('button[data-check-update]').length) {
            this.checkUpdate()
        }
    }

    checkUpdate() {
        $httpClient
            .make()
            .post($('button[data-check-update]').data('check-update-url'))
            .then(({ data }) => {
                if (!data.data) {
                    return
                }

                Object.keys(data.data).forEach((key) => {
                    const plugin = data.data[key]

                    const $button = $(`button[data-check-update="${plugin.name}"]`)

                    const url = $button.data('update-url').replace('__id__', plugin.id)

                    $button.data('update-url', url).show()
                })
            })
    }

    async activateOrDeactivatePlugin(url, reload = true) {
        return $httpClient
            .make()
            .put(url)
            .then(({ data }) => {
                Botble.showSuccess(data.message)

                if (reload) {
                    window.location.reload()
                }
            })
    }
}

$(() => {
    new PluginManagement().init()
})
