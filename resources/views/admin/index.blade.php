<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title>Админ — Компании</title>
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
  <h1>Компании (resti_core)</h1>

  @if (session('created_info'))
    @php($i = session('created_info'))
    <div class="flash">
      <div><b>Создано:</b> {{ $i['slug'] }} (БД: {{ $i['db'] }})</div>
      <div><b>API Secret (сохраните!):</b> <code>{{ $i['secret'] }}</code></div>
      <div><b>Authorization:</b> <code>Basic {{ $i['basic'] }}</code></div>
    </div>
  @endif

  <h2>Новая компания</h2>
  <form method="POST" action="{{ route('admin.store') }}">
    @csrf
    <div>
      <label>Название компании</label>
      <input type="text" name="name" required placeholder="Evorm SIA" value="{{ old('name') }}">
      @error('name') <div style="color:#c00">{{ $message }}</div> @enderror
    </div>
    <div>
      <label>Slug (латиница, дефис/подчёркивание)</label>
      <input type="text" name="slug" required placeholder="evorm" value="{{ old('slug') }}">
      @error('slug') <div style="color:#c00">{{ $message }}</div> @enderror
    </div>
    <div>
      <label>Имя БД (опционально, по умолчанию tenant_{slug})</label>
      <input type="text" name="db_name" placeholder="tenant_evorm" value="{{ old('db_name') }}">
    </div>
    <div>
      <label>Имя API ключа (опц.)</label>
      <input type="text" name="key_name" placeholder="primary" value="{{ old('key_name') }}">
    </div>
    <button type="submit">Создать</button>
  </form>

  <h2>Компании</h2>
  <table>
    <thead>
      <tr>
        <th>Название</th>
        <th>Slug</th>
        <th>Статус</th>
        <th>База</th>
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
        <td><a href="{{ route('admin.show', $c->slug) }}">Открыть</a></td>
      </tr>
    @empty
      <tr><td colspan="5">Пока пусто</td></tr>
    @endforelse
    </tbody>
  </table>
</body>
</html>
