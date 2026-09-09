<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f3f4f6; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg bg-white/80 backdrop-blur-md shadow-xl rounded-2xl p-8 border border-white/20">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">{{ $form->title }}</h1>
        
        <form id="dynamic-form" class="space-y-5">
            <!-- Fields will be injected here via JS -->
            <div id="fields-container" class="space-y-4"></div>
            
            <div id="error-container" class="hidden text-red-500 text-sm font-medium bg-red-50 p-3 rounded-lg border border-red-200"></div>
            <div id="success-container" class="hidden text-emerald-600 text-sm font-medium bg-emerald-50 p-3 rounded-lg border border-emerald-200"></div>

            <button type="submit" id="submit-btn" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-xl shadow-lg transition-all transform active:scale-95 flex justify-center mt-6">
                <span>Submit Response</span>
            </button>
        </form>
    </div>

    <script>
        const schema = {!! json_encode($form->publishedVersion->schema) !!};
        const formId = "{{ $form->uuid }}";
        const container = document.getElementById('fields-container');

        function renderFields() {
            container.innerHTML = '';
            schema.forEach(field => {
                let inputHtml = '';
                const required = field.required ? 'required' : '';
                const asterisk = field.required ? '<span class="text-red-500">*</span>' : '';
                
                if (field.type === 'select') {
                    const optionsHtml = (field.options || []).map(opt => `<option value="${opt}">${opt}</option>`).join('');
                    inputHtml = `<select name="${field.name}" id="${field.name}" ${required} class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 p-2.5 border transition-colors text-gray-700"><option value="">-- Select --</option>${optionsHtml}</select>`;
                } else if (field.type === 'radio') {
                    const optionsHtml = (field.options || []).map(opt => `
                        <label class="inline-flex items-center mr-4 mt-2 cursor-pointer">
                            <input type="radio" name="${field.name}" value="${opt}" ${required} class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                            <span class="ml-2 text-gray-700 text-sm font-medium">${opt}</span>
                        </label>
                    `).join('');
                    inputHtml = `<div>${optionsHtml}</div>`;
                } else if (field.type === 'checkbox') {
                    inputHtml = `
                        <label class="inline-flex items-center mt-2 cursor-pointer">
                            <input type="hidden" name="${field.name}" value="false">
                            <input type="checkbox" name="${field.name}" value="true" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-gray-700 text-sm font-medium">Yes</span>
                        </label>
                    `;
                } else {
                    const inputType = field.type === 'date' ? 'date' : (field.type === 'number' ? 'number' : (field.type === 'email' ? 'email' : 'text'));
                    inputHtml = `<input type="${inputType}" name="${field.name}" id="${field.name}" ${required} class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 p-2.5 border transition-colors text-gray-700">`;
                }

                container.innerHTML += `
                    <div class="field-wrapper" id="wrapper-${field.name}">
                        <label for="${field.name}" class="block text-sm font-semibold text-gray-700">${field.label} ${asterisk}</label>
                        ${inputHtml}
                    </div>
                `;
            });
        }

        renderFields();

        document.getElementById('dynamic-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const btn = document.getElementById('submit-btn');
            const errorDiv = document.getElementById('error-container');
            const successDiv = document.getElementById('success-container');
            
            btn.disabled = true;
            btn.innerHTML = 'Submitting...';
            errorDiv.classList.add('hidden');
            successDiv.classList.add('hidden');

            const formData = new FormData(e.target);
            const data = {};
            formData.forEach((value, key) => {
                if (value === "true") data[key] = true;
                else if (value === "false") data[key] = false;
                else data[key] = value;
            });

            try {
                const response = await fetch(`/api/v1/forms/${formId}/submissions`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (response.status === 202) {
                    successDiv.innerHTML = `Submission Accepted! Reference ID: <br><strong class="font-mono text-xs">${result.submission_id}</strong>`;
                    successDiv.classList.remove('hidden');
                    e.target.reset();
                } else if (response.status === 429) {
                     errorDiv.innerHTML = 'Too many requests. Please slow down and try again later.';
                     errorDiv.classList.remove('hidden');
                } else {
                    let errStr = result.message || 'An error occurred';
                    if (result.errors) {
                        errStr += '<ul class="list-disc pl-5 mt-2">';
                        for(const field in result.errors) {
                            errStr += `<li>${result.errors[field].join(', ')}</li>`;
                        }
                        errStr += '</ul>';
                    }
                    errorDiv.innerHTML = errStr;
                    errorDiv.classList.remove('hidden');
                }
            } catch (err) {
                errorDiv.innerHTML = 'Network error. Please try again.';
                errorDiv.classList.remove('hidden');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<span>Submit Response</span>';
            }
        });
    </script>
</body>
</html>
