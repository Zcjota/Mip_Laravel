import Swal from 'sweetalert2';

export const showAlert = (title, text, type) => {
  Swal.fire({
    title: title,
    text: text,
    icon: type,
    confirmButtonText: 'Aceptar'
  });
};