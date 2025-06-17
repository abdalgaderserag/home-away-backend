@extends('test.layouts.app')

@section('content')
<div class="container mx-auto max-w-md p-6">
  <h2 class="text-2xl mb-4">Register</h2>

  <div id="alerts"></div>

  <form id="register-form">
    @csrf
    <div class="mb-4">
      <label for="name" class="block text-sm font-medium">Name</label>
      <input type="text" id="name" name="name" class="border rounded w-full p-2" required>
    </div>

    <div class="mb-4">
      <label for="email" class="block text-sm font-medium">Email</label>
      <input type="email" id="email" name="email" class="border rounded w-full p-2">
    </div>

    <div class="mb-4">
      <label for="phone" class="block text-sm font-medium">Phone</label>
      <input type="text" id="phone" name="phone" class="border rounded w-full p-2">
    </div>

    <div class="mb-4">
      <label for="password" class="block text-sm font-medium">Password</label>
      <input type="password" id="password" name="password" class="border rounded w-full p-2" required>
    </div>

    <div class="mb-4">
      <span class="block text-sm font-medium">Type</span>
      <label class="inline-flex items-center mr-4">
        <input type="radio" name="type" value="client" checked class="form-radio">
        <span class="ml-2">Client</span>
      </label>
      <label class="inline-flex items-center">
        <input type="radio" name="type" value="designer" class="form-radio">
        <span class="ml-2">Designer</span>
      </label>
    </div>

    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Register</button>
  </form>
</div>

<script>
document.getElementById('register-form').addEventListener('submit', async e => {
  e.preventDefault();
  const alerts = document.getElementById('alerts');
  alerts.innerHTML = '';

  const data = {
    name: document.getElementById('name').value,
    email: document.getElementById('email').value || null,
    phone: document.getElementById('phone').value || null,
    password: document.getElementById('password').value,
    type: document.querySelector('input[name=type]:checked').value
  };

  try {
    const res = await fetch('/api/register', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    });

    const json = await res.json();

    if (!res.ok) {
      // display validation errors
      if (json.errors) {
        Object.values(json.errors).flat().forEach(msg => {
          const div = document.createElement('div');
          div.className = 'bg-red-100 text-red-700 p-2 mb-2 rounded';
          div.textContent = msg;
          alerts.appendChild(div);
        });
      } else if (json.message) {
        const div = document.createElement('div');
        div.className = 'bg-red-100 text-red-700 p-2 mb-2 rounded';
        div.textContent = json.message;
        alerts.appendChild(div);
      }
      return;
    }

    // success: store token and redirect
    localStorage.setItem('auth_token', json.token);
    window.location.href = '/';
  } catch (err) {
    const div = document.createElement('div');
    div.className = 'bg-red-100 text-red-700 p-2 mb-2 rounded';
    div.textContent = 'Registration failed. Please try again.';
    alerts.appendChild(div);
  }
});
</script>
