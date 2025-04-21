const form = document.getElementById('contactForm');
const popup = document.getElementById('popup');
const loading = document.getElementById('loading'); // Add a loading div in HTML

form.addEventListener('submit', function(e) {
  e.preventDefault(); // Stop the normal form submission

  const name = form.name.value.trim();
  const email = form.email.value.trim();
  const message = form.message.value.trim();

  // Frontend validation
  if (name.length < 3) {
    alert('Name must be at least 3 characters long.');
    return;
  }
  if (!email.includes('@') || !email.includes('.')) {
    alert('Please enter a valid email address.');
    return;
  }
  if (message.length < 10) {
    alert('Message must be at least 10 characters long.');
    return;
  }

  const formData = new FormData(form);

  // Show loading spinner
  loading.classList.remove('hidden');

  fetch('submit.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json()) // <--- changed from text() to json()
  .then(data => {
    console.log(data);
    if (data.status === 'success') {
      popup.innerText = data.message; // Show success message
      popup.classList.remove('hidden'); // Show popup
      setTimeout(() => {
        popup.classList.add('hidden'); // Hide popup after 3 seconds
        form.reset(); // Reset form
      }, 3000);
    } else {
      alert(data.message); // Show error if any
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Something went wrong. Please try again.');
  })
  .finally(() => {
    loading.classList.add('hidden'); // Hide loading spinner
  });
});

document.querySelector('#contactForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default form submission

    const formData = new FormData(this);

    fetch('submit.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
    })
    .catch(error => {
        alert('An error occurred: ' + error);
    });
});