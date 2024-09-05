'use strict'

$(document).ready(() => {
    $(document)
        .on('click', '#confirm-add-credit-button', (event) => {
            event.preventDefault()

            const button = $(event.currentTarget)
            const modal = button.closest('.modal')
            const form = modal.find('form')

            $httpClient
                .make()
                .withButtonLoading(button)
                .post(form.prop('action'), form.serialize())
                .then(({ data }) => {
                    Botble.showNotice('success', data.message)
                    modal.modal('hide')
                    form.get(0).reset()

                    $('#credit-histories').load(`${$('.page form').prop('action')} #credit-histories > *`)
                })
        })
        .on('click', '.step-item', (event) => {
            $(event.currentTarget).find('fieldset').slideToggle()
        })
        .on('show.bs.modal', '#edit-experience-modal', (e) => {
            const currentTarget = $(e.relatedTarget)
            const modal = $(e.currentTarget)

            $httpClient
                .make()
                .get(currentTarget.prop('href'))
                .then(({ data }) => {
                    modal.find('.modal-body').html(data)
                })
        })
        .on('show.bs.modal', '#edit-education-modal', (e) => {
            const currentTarget = $(e.relatedTarget)
            const modal = $(e.currentTarget)

            $httpClient
                .make()
                .get(currentTarget.prop('href'))
                .then(({ data }) => {
                    modal.find('.modal-body').html(data)
                })
        })
        .on('show.bs.modal', '#modal-confirm-delete', (e) => {
            const button = $(e.relatedTarget)
            const modal = $(e.currentTarget)

            modal.find('[data-bb-toggle="confirm-delete"]').data('url', button.prop('href'))
        })
        .on('click', '#confirm-add-education-button', (event) => {
            event.preventDefault()

            const button = $(event.currentTarget)
            const modal = button.closest('.modal')
            const form = modal.find('form')

            $httpClient
                .make()
                .withButtonLoading(button)
                .post(form.prop('action'), form.serialize())
                .then(({ data }) => {
                    Botble.showNotice('success', data.message)
                    modal.modal('hide')
                    form.get(0).reset()

                    $('#educations-table').load(`${$('.page form').prop('action')} #educations-table > *`)
                })
        })
        .on('click', '#confirm-edit-education-button', (event) => {
            event.preventDefault()

            const button = $(event.currentTarget)
            const modal = button.closest('.modal')
            const form = modal.find('form')

            $httpClient
                .make()
                .withButtonLoading(button)
                .post(form.prop('action'), form.serialize())
                .then(({ data }) => {
                    Botble.showNotice('success', data.message)
                    modal.modal('hide')
                    form.get(0).reset()

                    $('#educations-table').load(`${$('.page form').prop('action')} #educations-table > *`)
                })
        })
        .on('click', '#confirm-add-experience-button', (event) => {
            event.preventDefault()

            const button = $(event.currentTarget)
            const modal = button.closest('.modal')
            const form = modal.find('form')

            $httpClient
                .make()
                .withButtonLoading(button)
                .post(form.prop('action'), form.serialize())
                .then(({ data }) => {
                    Botble.showNotice('success', data.message)
                    modal.modal('hide')
                    form.get(0).reset()

                    $('#experiences-table').load(`${$('.page form').prop('action')} #experiences-table > *`)
                })
        })
        .on('click', '#confirm-edit-experience-button', (event) => {
            event.preventDefault()

            const button = $(event.currentTarget)
            const modal = button.closest('.modal')
            const form = modal.find('form')

            $httpClient
                .make()
                .withButtonLoading(button)
                .post(form.prop('action'), form.serialize())
                .then(({ data }) => {
                    Botble.showNotice('success', data.message)
                    modal.modal('hide')
                    form.get(0).reset()

                    $('#experiences-table').load(`${$('.page form').prop('action')} #experiences-table > *`)
                })
        })
        .on('click', '[data-bb-toggle="confirm-delete"]', (event) => {
            event.preventDefault()

            const button = $(event.currentTarget)

            $httpClient
                .make()
                .withButtonLoading(button)
                .delete(button.data('url'))
                .then(({ data }) => {
                    Botble.showNotice('success', data.message)
                    button.closest('.modal').modal('hide')

                    $('#educations-table').load(`${$('.page form').prop('action')} #educations-table > *`)
                    $('#experiences-table').load(`${$('.page form').prop('action')} #experiences-table > *`)
                })
        })
})
