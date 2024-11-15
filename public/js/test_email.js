document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('test-email-form');
    const alertContainer = document.getElementById('alert-container');
    const randomizeButton = document.getElementById('randomize-customers');
    const customersContainer = document.getElementById('customers-container');

    function showAlert(message, type = 'danger') {
        alertContainer.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
    }

    function getUrlParameter(name) {
        if (name === 'id') {
            // Wyodrębniamy ID kampanii z ścieżki URL
            const pathParts = window.location.pathname.split('/');
            return pathParts[pathParts.length - 2]; // Przedostatni element ścieżki
        }
        // Dla innych parametrów możesz zachować oryginalną logikę
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(name);
    }

    function fetchNewCustomers() {
        const campaignId = getUrlParameter('id');
        if (!campaignId) {
            showAlert('Brak ID kampanii w URL.');
            return;
        }

        fetch(`/campaign/${campaignId}/get-new-test-customers`)
            .then(response => response.json())
            .then(data => {
                updateCustomersView(data);
            })
            .catch(error => {
                console.error('Błąd podczas pobierania nowych klientów:', error);
                showAlert('Nie udało się pobrać nowych klientów.');
            });
    }

    function updateCustomersView(customers) {
        customersContainer.innerHTML = customers.map((customer, index) => `
            <div class="form-check">
                <input class="form-check-input" type="radio" name="selectedCustomer" 
                       id="customer${customer.id}" value="${customer.id}" ${index === 0 ? 'checked' : ''}>
                <label class="form-check-label" for="customer${customer.id}">
                    ${customer.email} - ${customer.imie} (${customer.product_nazwa})
                </label>
            </div>
        `).join('');
    }

    if (randomizeButton) {
        randomizeButton.addEventListener('click', fetchNewCustomers);
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        const campaignId = getUrlParameter('id');

        if (!campaignId) {
            showAlert('Brak ID kampanii w URL.');
            return;
        }

        fetch(`/campaign/${campaignId}/test-email`, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                showAlert(data.message, data.success ? 'success' : 'danger');
                if (data.success) {
                    form.reset();
                }
            })
            .catch(error => {
                console.error('Błąd:', error);
                showAlert('Wystąpił błąd podczas wysyłania e-maila.');
            });
    });

    // Inicjalne pobranie klientów
    //fetchNewCustomers();
});