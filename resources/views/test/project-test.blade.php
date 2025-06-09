<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>API Tester</title>
  <style>
    body { font-family: sans-serif; margin: 2rem; }
    label { display: block; margin-top: 1rem; }
    input, select, textarea { width: 100%; padding: .5rem; margin-top: .25rem; }
    button { margin-top: 1rem; padding: .5rem 1rem; }
    pre { background: #f4f4f4; padding: 1rem; white-space: pre-wrap; }
  </style>
</head>
<body>
  <h1>Project API Tester</h1>

  <label>
    API Token
    <input type="text" id="token" placeholder="Bearer token here…">
  </label>

  <label>
    HTTP Method
    <select id="method">
      <option>GET</option>
      <option>POST</option>
      <option>PUT</option>
      <option>PATCH</option>
      <option>DELETE</option>
    </select>
  </label>

  <label>
    Endpoint (relative to <code>/api</code>)
    <input type="text" id="endpoint" placeholder="/projects?status=published">
  </label>

  <label>
    JSON Body (for POST/PUT/PATCH)
    <textarea id="body" rows="6" placeholder='{"title":"My Project","min_price":100}'></textarea>
  </label>

  <button id="send">Send Request</button>

  <h2>Response</h2>
  <pre id="response">—</pre>

  <script>
    document.getElementById('send').addEventListener('click', async () => {
      const token = document.getElementById('token').value.trim();
      const method = document.getElementById('method').value;
      const endpoint = document.getElementById('endpoint').value.trim();
      let body = document.getElementById('body').value.trim();
      let options = { method, headers: {} };

      if (token) {
        options.headers['Authorization'] = 'Bearer ' + token;
      }

      if (['POST','PUT','PATCH'].includes(method) && body) {
        try {
          options.headers['Content-Type'] = 'application/json';
          options.body = JSON.stringify(JSON.parse(body));
        } catch (e) {
          return alert('Invalid JSON body');
        }
      }

      try {
        const res = await fetch(`/api${endpoint}`, options);
        const text = await res.text();
        let json;
        try { json = JSON.stringify(JSON.parse(text), null, 2); }
        catch { json = text; }
        document.getElementById('response').textContent =
          `HTTP ${res.status} ${res.statusText}\n\n` + json;
      } catch (err) {
        document.getElementById('response').textContent = 'Error: ' + err;
      }
    });
  </script>
</body>
</html>
