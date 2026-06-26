function openPelangganModal(){
  document.getElementById('modalTitle').innerText = 'Tambah Pelanggan';
  document.getElementById('pForm').reset();
  document.getElementById('pId').value = '';
  document.getElementById('pSubmitBtn').name = 'tambah';

  document.getElementById('modalBackdrop').classList.add('open');
}

function openEditPelangganModal(id, nama, telp, email, alamat){
  document.getElementById('modalTitle').innerText = 'Edit Pelanggan';

  document.getElementById('pId').value = id;
  document.getElementById('pNama').value = nama;
  document.getElementById('pTelp').value = telp;
  document.getElementById('pEmail').value = email;
  document.getElementById('pAlamat').value = alamat;
  document.getElementById('pSubmitBtn').name = 'edit';

  document.getElementById('modalBackdrop').classList.add('open');
}

function closePelangganModal(){
  document.getElementById('modalBackdrop').classList.remove('open');
}

function openDeletePelangganModal(id){
  document.getElementById('deletePelangganId').value = id;
  document.getElementById('deletePelangganModal').classList.add('open');
}

function closeDeletePelangganModal(){
  document.getElementById('deletePelangganModal').classList.remove('open');
}

document.addEventListener('DOMContentLoaded', function(){
  const modalBackdrop = document.getElementById('modalBackdrop');

  if(modalBackdrop){
    modalBackdrop.addEventListener('click', function(e){
      if(e.target === modalBackdrop){
        closePelangganModal();
      }
    });
  }
});