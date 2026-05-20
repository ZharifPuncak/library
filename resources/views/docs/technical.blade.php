@extends('docs.pdf-layout')

@section('title', 'Library Portal — Technical Documentation')

@section('content')

{{-- COVER --}}
<div class="cover">
    <div class="crest">Puncak Niaga · Library Portal</div>
    <h1>Technical Documentation</h1>
    <div class="sub">System Architecture, Data Model & Operations</div>
    <div class="meta">
        Version 1.0 &middot; Generated {{ now()->format('d F Y') }}<br>
        Laravel 10 · PHP 8.1+ · Tailwind CSS · Alpine.js
    </div>
</div>

<div class="page-break"></div>

{{-- TOC --}}
<div class="toc">
    <h2>Contents</h2>
    <span class="toc-row"><span class="num">1.</span><span class="ttl">System Overview</span></span>
    <span class="toc-row"><span class="num">2.</span><span class="ttl">Tech Stack</span></span>
    <span class="toc-row"><span class="num">3.</span><span class="ttl">Project Structure</span></span>
    <span class="toc-row"><span class="num">4.</span><span class="ttl">Database Schema</span></span>
    <span class="toc-row"><span class="num">5.</span><span class="ttl">Domain Models</span></span>
    <span class="toc-row"><span class="num">6.</span><span class="ttl">Controllers &amp; Routing</span></span>
    <span class="toc-row"><span class="num">7.</span><span class="ttl">Authentication &amp; Authorisation</span></span>
    <span class="toc-row"><span class="num">8.</span><span class="ttl">Frontend Architecture</span></span>
    <span class="toc-row"><span class="num">9.</span><span class="ttl">Storage &amp; File Handling</span></span>
    <span class="toc-row"><span class="num">10.</span><span class="ttl">Setup &amp; Deployment</span></span>
    <span class="toc-row"><span class="num">11.</span><span class="ttl">Operational Tasks</span></span>
    <span class="toc-row"><span class="num">12.</span><span class="ttl">Known Conventions &amp; Gotchas</span></span>
</div>

<div class="page-break"></div>

{{-- 1. OVERVIEW --}}
<h1>1. System Overview</h1>
<p>
    The Library Portal is a digital media management platform that lets staff curate and publish a collection of photographs,
    videos and books (PDFs / Office docs), surface them to authenticated viewers through a fast browse-and-search interface,
    and group items into named collections. It also includes a personal "My&nbsp;List" feature, a VR tour module, and an
    administrative back-office for users, categories, tags, and a hero slider.
</p>

<h3>Primary actors</h3>
<table>
    <tr><th>Role</th><th>Capabilities</th></tr>
    <tr><td><span class="pill pill-navy">Admin</span></td><td>Full CRUD on media, collections, categories, tags, slider and users. Sees all draft/published/archived rows.</td></tr>
    <tr><td><span class="pill pill-slate">User</span></td><td>Browse published media, view details, save items + collections to a personal "My&nbsp;List", change own password.</td></tr>
    <tr><td>Guest</td><td>Land on the marketing home page and reach the login screen. No media access.</td></tr>
</table>

{{-- 2. STACK --}}
<h1>2. Tech Stack</h1>
<table>
    <tr><th>Layer</th><th>Technology</th></tr>
    <tr><td>Language</td><td>PHP 8.1+</td></tr>
    <tr><td>Framework</td><td>Laravel 10.x</td></tr>
    <tr><td>Database</td><td>MySQL 5.7+ (or MariaDB equivalent)</td></tr>
    <tr><td>Frontend</td><td>Server-rendered Blade + Tailwind CSS (CDN) + Alpine.js</td></tr>
    <tr><td>Auth</td><td>Laravel's session/cookie auth (laravel/ui scaffolding)</td></tr>
    <tr><td>File storage</td><td>Local <code>storage/app/public</code> with <code>php artisan storage:link</code></td></tr>
    <tr><td>Notable packages</td><td>livewire/livewire, doctrine/dbal, barryvdh/laravel-dompdf, james-heinrich/getid3</td></tr>
</table>

{{-- 3. STRUCTURE --}}
<h1>3. Project Structure</h1>
<pre>
app/
├── Http/
│   ├── Controllers/        — 11 controllers (Media, Collection, MyList, User, Category, Tag, Slider, Profile, Home, Login, Vr)
│   ├── Middleware/         — RedirectIfAuthenticated, PreventBackHistory, ...
│   └── Kernel.php          — middleware aliases
├── Models/                 — Media, Collection, MediaDetail, Category, Tag, Slider, User
└── Providers/

database/
├── migrations/             — schema (renamed assets→media, FK columns updated)
└── seeders/AdminUserSeeder.php

resources/views/
├── auth/login.blade.php
├── layouts/app.blade.php   — single layout for every page
├── home.blade.php          — guest/landing
├── users/                  — media browse + show + create + edit + collections/* + mylist/*
├── user/                   — admin user management
├── collections/            — (legacy, see users/collections)
├── categories/index.blade.php
├── tags/index.blade.php
├── slider/index.blade.php
├── profile/edit.blade.php
└── docs/                   — PDF templates (this file)

routes/web.php              — all routes (no API routes used)
</pre>

{{-- 4. SCHEMA --}}
<h1>4. Database Schema</h1>

<h3>users</h3>
<table>
    <tr><th>Column</th><th>Type</th><th>Notes</th></tr>
    <tr><td>id</td><td>bigint</td><td>PK</td></tr>
    <tr><td>name</td><td>varchar</td><td></td></tr>
    <tr><td>username</td><td>varchar</td><td>unique, used for login</td></tr>
    <tr><td>email</td><td>varchar</td><td>unique</td></tr>
    <tr><td>password</td><td>varchar</td><td>bcrypt hash</td></tr>
    <tr><td>role</td><td>varchar</td><td><code>admin</code> | <code>user</code></td></tr>
    <tr><td>remember_token, email_verified_at, timestamps</td><td></td><td></td></tr>
</table>

<h3>media</h3>
<table>
    <tr><th>Column</th><th>Type</th><th>Notes</th></tr>
    <tr><td>id</td><td>bigint</td><td>PK</td></tr>
    <tr><td>uuid</td><td>uuid</td><td>unique; route binding key</td></tr>
    <tr><td>type</td><td>varchar</td><td><code>photo</code> | <code>video</code> | <code>ebook</code></td></tr>
    <tr><td>status</td><td>varchar(20)</td><td>indexed; <code>draft</code> | <code>published</code> | <code>archived</code></td></tr>
    <tr><td>title</td><td>varchar</td><td></td></tr>
    <tr><td>file_path</td><td>varchar(nullable)</td><td>relative path under <code>storage/app/public</code></td></tr>
    <tr><td>file_url</td><td>varchar(nullable)</td><td>external URL for "Use link" mode</td></tr>
    <tr><td>date</td><td>date(nullable)</td><td>optional event date</td></tr>
    <tr><td>thumbnail_path</td><td>varchar(nullable)</td><td></td></tr>
    <tr><td>timestamps</td><td></td><td></td></tr>
</table>

<h3>media_details</h3>
<p>Flexible key/value metadata attached to a media row. Keys in use: <code>views</code>, <code>collection</code>, <code>location</code> (book only), and any custom ad-hoc keys via <code>$media->getDetail($key, $default)</code>.</p>
<table>
    <tr><th>Column</th><th>Type</th><th>Notes</th></tr>
    <tr><td>id</td><td>bigint</td><td>PK</td></tr>
    <tr><td>media_id</td><td>bigint</td><td>FK media, cascade delete</td></tr>
    <tr><td>key</td><td>varchar</td><td></td></tr>
    <tr><td>value</td><td>text</td><td></td></tr>
    <tr><td>timestamps</td><td></td><td></td></tr>
</table>

<h3>Pivot tables</h3>
<table>
    <tr><th>Table</th><th>Columns</th></tr>
    <tr><td>media_category</td><td>id, media_id, category_id</td></tr>
    <tr><td>media_tag</td><td>id, media_id, tag_id</td></tr>
    <tr><td>my_list_items</td><td>id, user_id, media_id, timestamps · unique(user_id, media_id)</td></tr>
    <tr><td>my_list_collections</td><td>id, user_id, collection_id, timestamps · unique(user_id, collection_id)</td></tr>
</table>

<h3>Lookup tables</h3>
<table>
    <tr><th>Table</th><th>Columns</th></tr>
    <tr><td>categories</td><td>id, name, timestamps</td></tr>
    <tr><td>tags</td><td>id, name, timestamps</td></tr>
    <tr><td>collections</td><td>id, uuid (unique), name (unique), timestamps</td></tr>
    <tr><td>sliders</td><td>id, title, slider_pic, timestamps</td></tr>
</table>

<div class="callout">
    <strong>Collection link via metadata.</strong> Collections do not use a pivot table. The <code>media_details</code> row with
    <code>key='collection', value=&lt;collection name&gt;</code> tags a media item as belonging to that collection. The
    <code>Collection</code> model exposes a UUID for routing; the join still happens through <code>media_details.value</code>.
</div>

{{-- 5. MODELS --}}
<h1>5. Domain Models</h1>

<h3>Media (app/Models/Media.php)</h3>
<ul>
    <li><strong>Constants</strong>: <code>Media::STATUSES = ['draft', 'published', 'archived']</code>.</li>
    <li><strong>Route key</strong>: <code>uuid</code> — Collection URLs use <code>/collections/{uuid}</code>.</li>
    <li><strong>Auto UUID</strong>: <code>booted()</code> assigns <code>uuid</code> on create if not supplied.</li>
    <li><strong>Accessors</strong>:
        <ul>
            <li><code>$media->resource_url</code> — external link or storage upload, whichever is set.</li>
            <li><code>$media->thumbnail_url</code> — explicit thumbnail → photo's source → default logo.</li>
            <li><code>$media->has_real_thumbnail</code> — true when a non-fallback cover exists.</li>
            <li><code>$media->collection</code> — reads <code>details</code> key='collection'.</li>
        </ul>
    </li>
    <li><strong>Scopes</strong>:
        <ul>
            <li><code>Media::published()</code> — restricts to <code>status='published'</code>.</li>
            <li><code>Media::inCollection($name)</code> — restricts to a collection by name.</li>
        </ul>
    </li>
    <li><strong>Helpers</strong>: <code>getDetail($key, $default)</code>, <code>incrementViews()</code>.</li>
</ul>

<h3>Collection</h3>
<ul>
    <li>UUID route key, auto-generated on create.</li>
    <li><code>name</code> is unique; renaming propagates to all <code>media_details</code> rows on update.</li>
</ul>

<h3>User</h3>
<ul>
    <li><code>isAdmin()</code> — case-insensitive: matches <code>'admin'</code>, <code>'1'</code> or integer <code>1</code>.</li>
    <li><code>myList()</code> — belongsToMany(Media) via <code>my_list_items</code>.</li>
    <li><code>myListCollections()</code> — belongsToMany(Collection) via <code>my_list_collections</code>.</li>
    <li><code>mediaUploaded()</code> — hasMany Media (uploader column <code>uploaded_by</code>, still present in fillable on Media table for future use).</li>
</ul>

{{-- 6. CONTROLLERS --}}
<h1>6. Controllers &amp; Routing</h1>

<h3>Public/Auth routes</h3>
<table>
    <tr><th>Method &amp; path</th><th>Name</th><th>Controller</th></tr>
    <tr><td>GET /, /home</td><td>home</td><td>HomeController@index</td></tr>
    <tr><td>GET /login</td><td>login</td><td>LoginController</td></tr>
    <tr><td>POST /login</td><td></td><td>LoginController</td></tr>
    <tr><td>POST /logout</td><td>logout</td><td>LoginController</td></tr>
</table>

<h3>Authenticated routes</h3>
<table>
    <tr><th>Method &amp; path</th><th>Name</th><th>Purpose</th></tr>
    <tr><td>GET /collections</td><td>media.index</td><td>Filtered/paginated browse</td></tr>
    <tr><td>GET /collections (+ create/show/edit/update/destroy/download)</td><td>collections.*</td><td>Collection-centric add/edit (multi-file upload, shared metadata). UUID-bound. Admin-gated for CUD.</td></tr>
    <tr><td>GET /my-list, /my-list/add, POST /my-list/sync</td><td>mylist.*</td><td>Per-user saved items</td></tr>
    <tr><td>GET /users, ... (CRUD)</td><td>users.*</td><td>Admin only</td></tr>
    <tr><td>GET /categories, /tags, /slider (+ CRUD)</td><td></td><td>Admin only</td></tr>
    <tr><td>GET /profile, PUT /profile/password</td><td>profile.*</td><td>Own password change</td></tr>
    <tr><td>GET /vr, /vr-test</td><td>vr.*</td><td>VR tour module</td></tr>
</table>

<h3>Admin enforcement</h3>
<p>Every admin-only controller uses the same constructor pattern:</p>
<pre>
public function __construct()
{
    $this->middleware('auth');
    $this->middleware(function ($request, $next) {
        abort_unless(auth()->user()?->isAdmin(), 403);
        return $next($request);
    });
}
</pre>
<p>For mixed controllers (e.g. <code>MediaController</code> where listing/showing are public-auth but create/edit are admin), the second middleware is scoped via <code>->only([...])</code>.</p>

{{-- 7. AUTHZ --}}
<h1>7. Authentication &amp; Authorisation</h1>
<ul>
    <li>Login is by <code>username</code> (not email).</li>
    <li>No public registration. Admins create users from <code>/users/create</code>; the initial admin is seeded by <code>AdminUserSeeder</code>.</li>
    <li>The <code>role</code> column is the single source of truth. <code>$user->isAdmin()</code> drives every authz check.</li>
    <li>Logout uses SweetAlert2 confirmation; delete actions for media + collections use a matching SweetAlert pattern.</li>
</ul>

<div class="callout warn">
    <strong>Hardening.</strong> Throttling on <code>POST /login</code> is provided by Laravel's <code>AuthenticatesUsers</code> trait. Keep
    <code>APP_DEBUG=false</code> and <code>APP_ENV=production</code> in production. The <code>storage:link</code> symlink must exist for uploads to resolve.
</div>

{{-- 8. FRONTEND --}}
<h1>8. Frontend Architecture</h1>
<p>One Blade layout — <code>resources/views/layouts/app.blade.php</code> — wraps every page.</p>
<ul>
    <li><strong>Tailwind</strong> is loaded via the CDN script with an inline theme extension (<code>lib-navy</code>, <code>lib-sky</code>, <code>lib-light</code> custom colors).</li>
    <li><strong>Alpine.js</strong> is loaded via CDN. Used for: collapsible admin sidebar (state persisted to <code>localStorage</code>), category/tag pill multi-select, source toggle on the add-media form, SweetAlert confirm wrappers.</li>
    <li><strong>SweetAlert2</strong> powers all destructive confirmations.</li>
    <li><strong>Custom pagination view</strong>: <code>resources/views/vendor/pagination/library.blade.php</code>; invoked via <code>$paginator->links('vendor.pagination.library')</code>.</li>
    <li><strong>Admin sidebar</strong> is a fixed left column (collapsible <code>w-56</code> ↔ <code>w-16</code>) on <code>md+</code>, an offcanvas drawer on smaller screens.</li>
</ul>

{{-- 9. STORAGE --}}
<h1>9. Storage &amp; File Handling</h1>
<table>
    <tr><th>What</th><th>Where</th><th>Cap</th></tr>
    <tr><td>Media files (uploaded)</td><td><code>storage/app/public/media</code></td><td>50 MB</td></tr>
    <tr><td>Thumbnails</td><td><code>storage/app/public/media/thumbnails</code></td><td>1 MB</td></tr>
    <tr><td>Slider images</td><td><code>storage/app/public/sliders</code></td><td>25 MB</td></tr>
    <tr><td>Collection ZIP exports</td><td><code>storage/app</code> (temp, deleted after send)</td><td>—</td></tr>
</table>
<p>Allowed file types are scoped by the chosen media type:</p>
<table>
    <tr><th>Media type</th><th>Allowed extensions</th></tr>
    <tr><td>photo</td><td>jpg, jpeg, png, gif, webp, bmp</td></tr>
    <tr><td>video</td><td>mp4, webm, mov, avi, mkv, m4v</td></tr>
    <tr><td>ebook</td><td>pdf, doc, docx, xls, xlsx</td></tr>
</table>
<p>Files are deleted from disk when the corresponding media row is destroyed. Collections that are deleted release their tag from <code>media_details</code> but leave the files intact.</p>

{{-- 10. SETUP --}}
<h1>10. Setup &amp; Deployment</h1>

<h3>Local setup</h3>
<pre>
git clone <repo>
cd library
composer install
cp .env.example .env
php artisan key:generate
# configure DB credentials in .env
php artisan migrate
php artisan db:seed --class=AdminUserSeeder
php artisan storage:link
php artisan serve
</pre>

<h3>Default admin credentials</h3>
<table>
    <tr><th>Field</th><th>Default</th><th>Override</th></tr>
    <tr><td>Username</td><td><code>admin</code></td><td><code>SEED_ADMIN_USERNAME</code></td></tr>
    <tr><td>Email</td><td><code>admin@example.com</code></td><td><code>SEED_ADMIN_EMAIL</code></td></tr>
    <tr><td>Password</td><td><em>Set per deployment</em></td><td><code>SEED_ADMIN_PASSWORD</code> is required in production</td></tr>
</table>

<div class="callout danger">
    <strong>Production checklist:</strong>
    <ul>
        <li>Change the default admin password immediately after first login.</li>
        <li>Set <code>APP_ENV=production</code>, <code>APP_DEBUG=false</code>, <code>APP_URL=https://...</code>, and <code>SESSION_SECURE_COOKIE=true</code> in <code>.env</code>.</li>
        <li>Install production dependencies with <code>composer install --no-dev --optimize-autoloader</code>.</li>
        <li>Run <code>php artisan config:cache</code>, <code>route:cache</code>, <code>view:cache</code>.</li>
        <li>Ensure <code>storage/</code> and <code>bootstrap/cache/</code> are writable by the web server.</li>
        <li>Configure a real session store (Redis or DB) if running multiple app servers.</li>
        <li>Tune PHP <code>upload_max_filesize</code> and <code>post_max_size</code> &gt; 50 MB for media uploads.</li>
    </ul>
</div>

{{-- 11. OPS --}}
<h1>11. Operational Tasks</h1>

<table>
    <tr><th>Task</th><th>Command</th></tr>
    <tr><td>Migrate fresh</td><td><code>php artisan migrate:fresh --seed</code></td></tr>
    <tr><td>Clear caches</td><td><code>php artisan optimize:clear</code></td></tr>
    <tr><td>Re-seed only the admin row</td><td><code>php artisan db:seed --class=AdminUserSeeder</code></td></tr>
    <tr><td>Regenerate storage symlink</td><td><code>php artisan storage:link</code></td></tr>
    <tr><td>Regenerate these PDFs</td><td><code>php artisan docs:generate</code></td></tr>
</table>

{{-- 12. GOTCHAS --}}
<h1>12. Known Conventions &amp; Gotchas</h1>
<ul>
    <li><strong>UUID route binding</strong>: <code>Media</code> and <code>Collection</code> resolve via <code>uuid</code>, not <code>id</code>. Always pass the model instance to <code>route()</code>, not the integer id.</li>
    <li><strong>Status visibility</strong>: Non-admins never see <code>draft</code> or <code>archived</code> rows in any listing or related-items panel.</li>
    <li><strong>Related items</strong>: A media item must share <em>every</em> category AND <em>every</em> tag of the current item to appear in Related Items. If either set is empty on the current item, the section is hidden.</li>
    <li><strong>Categories &amp; tags</strong>: Cannot be deleted while still attached to any media — the controller refuses and the UI greys out the Delete button.</li>
    <li><strong>Collections deletion</strong>: Removes the <code>media_details</code> rows linking media to the collection, then deletes the collection. The media files themselves are not touched.</li>
    <li><strong>Sidebar state</strong>: Collapsed/expanded saved to <code>localStorage</code> key <code>adminSidebarCollapsed</code>.</li>
    <li><strong>View persistence</strong>: Grid/list toggle on <code>/collections</code> stored in cookie <code>media_view</code> for 30 days.</li>
</ul>

<div class="footer-note">
    © {{ date('Y') }} Puncak Niaga Library Portal — Technical Documentation
</div>

@endsection
