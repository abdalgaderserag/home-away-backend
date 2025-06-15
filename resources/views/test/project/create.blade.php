@extends('test.layouts.app')

@section('content')
<div class="container mx-auto max-w-md p-6">
  <h2 class="text-2xl mb-4">create project</h2>
<div id="alerts"></div>
    <h3>working on project #</h3>
      <input type="number" id="proId" name="proID" class="border rounded w-full p-2"> <button id="draft">create draft</button>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
  <form id="register-form">
    @csrf
    <div class="mb-4">
      <label for="title" class="block text-sm font-medium">title</label>
      <input type="text" id="title" name="title" class="border rounded w-full p-2" required>
    </div>

    <div class="mb-4">
      <label for="description" class="block text-sm font-medium">description</label>
      <input type="text" id="description" name="description" class="border rounded w-full p-2" required>
    </div>

    <div class="mb-4">
      <label for="space" class="block text-sm font-medium">space</label>
      <input type="number" id="space" name="space" class="border rounded w-full p-2" required>
    </div>

    <div class="mb-4">
      <label for="min_price" class="block text-sm font-medium">min_price</label>
      <input type="number" id="min_price" name="min_price" class="border rounded w-full p-2" required>
    </div>

    <div class="mb-4">
      <label for="max_price" class="block text-sm font-medium">max_price</label>
      <input type="number" id="max_price" name="max_price" class="border rounded w-full p-2" required>
    </div>

    <div class="mb-4">
      <label for="unit_type" class="block text-sm font-medium">unit_type</label>
      <select name="unit_type" id="unit_type">
      @foreach($units as $unit)
      <option value="{{ $unit->id }}">{{ $unit->type }}</option>
      @endforeach
      </select><br/>
    </div>

    <div class="mb-4">
      <label for="location" class="block text-sm font-medium">location</label>
      <select name="location" id="location">
      @foreach($locations as $location)
      <option value="{{ $location->id }}">{{ $location->city }}</option>
      @endforeach
      </select><br/>
    </div>

    <div class="mb-4">
      <label for="skill" class="block text-sm font-medium">skill</label>
      <select name="skill" id="skill">
      @foreach($skills as $skill)
      <option value="{{ $skill->id }}">{{ $skill->name }}</option>
      @endforeach
      </select><br/>
    </div>

    <div class="mb-4">
      <label for="deadline" class="block text-sm font-medium">deadline</label>
      <input type="date" id="deadline" name="deadline" class="border rounded w-full p-2" required>
    </div>



    <div class="mb-4">
      <span class="block text-sm font-medium">Resources</span>
      <label class="inline-flex items-center mr-4">
        <input type="checkbox" id="resource" name="resource" checked>
      </label>
    </div>

    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">save</button>
  </form>
</div>

<script>

const token = localStorage.getItem('auth_token');
      document.getElementById('draft').addEventListener('click', async e =>{
        let method = 'get';
        let token = localStorage.auth_token;
      let options = { method, headers: {} };
        options.headers['Content-Type'] = 'application/json'


      if (token) {
        options.headers['Authorization'] = 'Bearer ' + token;
      }

      try{
        const res = await fetch(`/api/projects/create`,options);
        const text = await res.text();
        let json;
        try { json = JSON.parse(text); }
        catch { json = text; }
        console.log(json);
        document.getElementById('proId').value = json.project.id;
      } catch (err){
        console.log(err);
      }
      });

document.getElementById('register-form').addEventListener('submit', async e => {
  e.preventDefault();
const alerts = document.getElementById('alerts');
  alerts.innerHTML = '';
  const data = {
    title: document.getElementById('title').value,
    description: document.getElementById('description').value,
    unit_type_id: Number(document.getElementById('unit_type').value),
    space: document.getElementById('space').value,
    location_id: Number(document.getElementById('location').value),
    deadline: document.getElementById('deadline').value,
    min_price: document.getElementById('min_price').value,
    max_price: document.getElementById('max_price').value,
    skill_id: Number(document.getElementById('skill').value),
    resource: document.getElementById('resource').checked
  };


  try {
    let id = document.getElementById('proId').value;
    if(!id){
        alert("add draft project id");
        throw 'asd'
    }
    let token = localStorage.auth_token;
    const res = await fetch('/api/projects/'+ id +'/save', {
      method: 'PUT',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization' : 'Bearer '+ token
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
      }
      return;
    }
    const dm = "the project with title" + json.project.title + "have been created and waiting for approval by support"
    alert(dm);
  } catch (err) {
    const div = document.createElement('div');
    div.className = 'bg-red-100 text-red-700 p-2 mb-2 rounded';
    div.textContent = 'request failed. Please try again.';
    alerts.appendChild(div);
  }
});
</script>
