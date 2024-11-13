let currentStep = 1;
const totalSteps = 5;
let skippedStep4 = false; // Variable para rastrear si el paso 4 fue omitido

function showStep(step) {
    document.querySelectorAll('.step').forEach((el) => {
        el.classList.remove('active');
    });
    document.getElementById(`step${step}`).classList.add('active');
    updateProgress(step);
}

function updateProgress(step) {
    const progress = ((step - 1) / (totalSteps - 1)) * 100;
    document.getElementById('progress').style.width = `${progress}%`;
}

function isEmailValid(email) {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailPattern.test(email);
}

function validateCurrentStep() {
    const currentStepElement = document.getElementById(`step${currentStep}`);
    const inputs = currentStepElement.querySelectorAll('input[required], select[required]');
    let isValid = true;
    let firstInvalidInput = null;

    inputs.forEach(input => {
        input.style.border = ''; // Reiniciar el borde del campo

        if (input.type === 'email') {
            if (!isEmailValid(input.value)) {
                isValid = false;
                if (!firstInvalidInput) firstInvalidInput = input;
                input.style.border = '2px solid red';
                alert(`Por favor, ingrese un correo electrónico válido en el campo Email o Email Laboral".`);
            }
        } else if (input.value.trim() === '') {
            isValid = false;
            if (!firstInvalidInput) firstInvalidInput = input;
            input.style.border = '2px solid red';
            alert(`Complete todos los campos.`);
        }
    });

    if (firstInvalidInput) {
        firstInvalidInput.focus();
    }

    return isValid;
}

function removeRequiredFromStep4() {
    // Quitar el atributo `required` de los campos de la etapa 4
    document.querySelectorAll('#step4 input, #step4 select').forEach(input => {
        input.removeAttribute('required');
    });
}

function addRequiredToStep4() {
    // Agregar el atributo `required` de nuevo a los campos de la etapa 4
    document.querySelectorAll('#step4 input, #step4 select').forEach(input => {
        input.setAttribute('required', 'required');
    });
}

function nextStep() {
    if (validateCurrentStep()) {
        if (currentStep === 3) {
            const vinculacion = document.getElementById('vinculacion').value;
            if (vinculacion === 'NO') {
                currentStep = 5; // Saltar al paso 5
                skippedStep4 = true; // Marcar que se omitió el paso 4
                removeRequiredFromStep4(); // Remover `required` de los campos del paso 4
                showStep(currentStep);
                return;
            } else {
                addRequiredToStep4(); // Asegurarse de que el paso 4 tenga `required` si es necesario
            }
        }
        
        currentStep++;
        if (currentStep > totalSteps) currentStep = totalSteps;
        showStep(currentStep);
    }
}

function prevStep() {
    if (currentStep === 5 && skippedStep4) {
        currentStep = 3; // Volver al paso 3 si se omitió el paso 4
        skippedStep4 = false; // Reiniciar la variable
    } else {
        currentStep--;
    }
    
    if (currentStep < 1) currentStep = 1;
    showStep(currentStep);
}

function resetForm() {
    if (confirm('¿Está seguro de que desea borrar el formulario?')) {
        document.getElementById('formulario').reset();
        currentStep = 1;
        showStep(currentStep);
    }
}

showStep(currentStep); // Inicializar el formulario en el primer paso
