<!doctype html>
<html lang="lv">
<head>
  <meta charset="utf-8">
  <title>Administrēšana — Uzņēmumi</title>
  <style>
    body { font-family: system-ui, sans-serif; max-width: 960px; margin: 32px auto; }
    table { width: 100%; border-collapse: collapse; margin-top: 16px; }
    th, td { border: 1px solid #ddd; padding: 8px; }
    th { background: #f6f6f6; text-align: left; }
    .flash { background: #eefaf0; border: 1px solid #c7efd1; padding: 12px; margin-bottom: 16px; }
    form > div { margin-bottom: 8px; }
    input[type=text] { width: 100%; padding: 6px 8px; }
    button { padding: 8px 12px; }
  </style>
</head>
<body>
  <h1>Uzņēmumi</h1>

  @if (session('created_info'))
    @php($i = session('created_info'))
    <div class="flash">
      <div><b>Izveidots:</b> {{ $i['slug'] }} (DB: {{ $i['db'] }})</div>
      <div><b>API Secret (saglabājiet!):</b> <code>{{ $i['secret'] }}</code></div>
      <div><b>Authorization:</b> <code>Basic {{ $i['basic'] }}</code></div>
    </div>
  @endif

  <h2>Jauns uzņēmums</h2>
  <form method="POST" action="{{ route('admin.store') }}">
    @csrf
    <div>
      <label>Uzņēmuma nosaukums</label>
      <input type="text" name="name" required placeholder="Evorm SIA" value="{{ old('name') }}">
      @error('name') <div style="color:#c00">{{ $message }}</div> @enderror
    </div>
    <div>
      <label>Slug (latīņu burti, domuzīme/pasvītrojums)</label>
      <input type="text" name="slug" required placeholder="evorm" value="{{ old('slug') }}">
      @error('slug') <div style="color:#c00">{{ $message }}</div> @enderror
    </div>
    <div>
      <label>DB nosaukums (pēc izvēles, pēc noklusējuma tenant_{slug})</label>
      <input type="text" name="db_name" placeholder="tenant_evorm" value="{{ old('db_name') }}">
    </div>
    <div>
      <label>API atslēgas nosaukums (pēc izvēles)</label>
      <input type="text" name="key_name" placeholder="primary" value="{{ old('key_name') }}">
    </div>
    <button type="submit">Izveidot</button>
  </form>

  <h2>Uzņēmumi</h2>
  <table>
    <thead>
      <tr>
        <th>Nosaukums</th>
        <th>Slug</th>
        <th>Statuss</th>
        <th>Datu bāze</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
    @forelse($companies as $c)
      <tr>
        <td>{{ $c->name }}</td>
        <td>{{ $c->slug }}</td>
        <td>{{ $c->status }}</td>
        <td>{{ $c->tenantRegistry->db_name ?? '—' }}</td>
        <td><a href="{{ route('admin.show', $c->slug) }}">Atvērt</a></td>
      </tr>
    @empty
      <tr><td colspan="5">Pagaidām tukšs</td></tr>
    @endforelse
    </tbody>
  </table>
</body>
</html>
