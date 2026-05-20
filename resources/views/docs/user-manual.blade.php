@extends('docs.pdf-layout')

@section('title', 'Library Portal — User Manual')

@section('content')

{{-- COVER --}}
<div class="cover">
    <div class="crest">Puncak Niaga · Library Portal</div>
    <h1>User Manual</h1>
    <div class="sub">How to use the digital library</div>
    <div class="meta">
        Version 1.0 &middot; {{ now()->format('d F Y') }}<br>
        For staff and administrators
    </div>
</div>

<div class="page-break"></div>

<div class="toc">
    <h2>Contents</h2>
    <span class="toc-row"><span class="num">1.</span><span class="ttl">Getting Started</span></span>
    <span class="toc-row"><span class="num">2.</span><span class="ttl">Browsing Media</span></span>
    <span class="toc-row"><span class="num">3.</span><span class="ttl">Viewing a Single Item</span></span>
    <span class="toc-row"><span class="num">4.</span><span class="ttl">Collections</span></span>
    <span class="toc-row"><span class="num">5.</span><span class="ttl">My&nbsp;List</span></span>
    <span class="toc-row"><span class="num">6.</span><span class="ttl">Your Profile</span></span>
    <span class="toc-row"><span class="num">7.</span><span class="ttl">Admin: Managing Media</span></span>
    <span class="toc-row"><span class="num">8.</span><span class="ttl">Admin: Categories &amp; Tags</span></span>
    <span class="toc-row"><span class="num">9.</span><span class="ttl">Admin: Users</span></span>
    <span class="toc-row"><span class="num">10.</span><span class="ttl">Admin: Slider</span></span>
    <span class="toc-row"><span class="num">11.</span><span class="ttl">Tips &amp; Troubleshooting</span></span>
</div>

<div class="page-break"></div>

{{-- 1. GETTING STARTED --}}
<h1>1. Getting Started</h1>

<h3>Signing in</h3>
<ol>
    <li>Open the Library Portal in your browser.</li>
    <li>On the landing page, click <strong>LOGIN</strong> (top-right).</li>
    <li>Enter your <strong>username</strong> (not email) and password.</li>
    <li>Tick <strong>Remember me</strong> to stay signed in across sessions on this device.</li>
    <li>Click <strong>Login</strong>. You'll land on the media browser.</li>
</ol>

<h3>Forgot your password?</h3>
<p>Ask an administrator to reset it for you from the Users page. If you <em>are</em> an admin, sign in once with the seeded password (provided to you on first deployment) and change it from <strong>Profile → Change password</strong>.</p>

<h3>Top-bar navigation</h3>
<ul>
    <li><strong>Library Portal logo</strong> &mdash; returns to the home page.</li>
    <li><strong>Your name (right)</strong> &mdash; opens a dropdown with <strong>Profile</strong> and <strong>Logout</strong>.</li>
</ul>

<h3>Sidebar (left)</h3>
<p>On medium screens and up, the sidebar shows your sections. Use the chevron at the top to collapse it to icons-only for more workspace. On small screens, tap the menu label at the top to slide it in as a drawer.</p>

{{-- 2. BROWSING --}}
<h1>2. Browsing Media</h1>
<p>The <strong>Media</strong> page is your starting point for finding items in the library.</p>

<h3>Categories sidebar (page-level)</h3>
<p>The narrow left panel on the media page lets you scope by category. Counts beside each entry show how many items match.</p>

<h3>Search</h3>
<p>Use the search bar at the top of the categories card. Type your query and press <strong>Search</strong>; clear it with the <strong>Clear</strong> link or by emptying the box (the page auto-resets once empty).</p>

<h3>Filters</h3>
<table>
    <tr><th>Pill</th><th>What it does</th></tr>
    <tr><td><span class="pill pill-sky">All</span></td><td>Removes category filter.</td></tr>
    <tr><td>Annual Reports / Magazines / ...</td><td>Filters by category — select multiple to widen the match.</td></tr>
    <tr><td>The <strong>⋯</strong> button</td><td>Reveals the rest of the categories when there are more than five.</td></tr>
</table>

<h3>Tag filter (left card)</h3>
<p>Tap any hashtag chip to add it to the URL. Click it again to remove. Use <strong>Clear</strong> to drop all selected tags.</p>

<h3>Sort &amp; view</h3>
<ul>
    <li><strong>Newest / Oldest</strong> (right-side pill) — flips the sort order on the date the row was added.</li>
    <li><strong>Grid / List</strong> toggle (square-tile icon, also on the right) — switches between thumbnail tiles and a compact row layout. Your choice is remembered for 30 days.</li>
</ul>

<h3>Pagination</h3>
<p>12 items per page. The pagination row shows "Showing X – Y of Z results"; click the numbered buttons to jump.</p>

<div class="callout">
    <strong>Tip.</strong> Filters combine. You can ask for "books in the Annual Reports category tagged <code>#corporate</code> sorted oldest first" — every selector you click is added to the URL.
</div>

{{-- 3. SHOW PAGE --}}
<h1>3. Viewing a Single Item</h1>
<p>Click any tile or row to open the item's details page.</p>

<h3>What you'll see</h3>
<ul>
    <li><strong>Title card</strong> — small thumbnail, date, title, and a type badge ("PHOTO", "VIDEO", "BOOK").</li>
    <li><strong>Viewer</strong> — image, video player, or PDF iframe depending on the type. Photos and books also have a fullscreen button.</li>
    <li><strong>Details (right)</strong> — type, status, dates, view count, optional location and author, the categories and tags it belongs to, and any collection it's in.</li>
    <li><strong>Related items</strong> — items that share the same set of categories AND tags appear below the viewer.</li>
    <li><strong>Actions card (right)</strong> — Edit Media (admins), Download (only when it was uploaded directly), and Add to / Remove from My List.</li>
</ul>

<h3>Back navigation</h3>
<p>The top-left link is contextual — "Back to media", "Back to photos/videos/books", or "Back to collections" depending on how you got here.</p>

{{-- 4. COLLECTIONS --}}
<h1>4. Collections</h1>

<h3>Browsing collections</h3>
<ol>
    <li>Use the sidebar <strong>Collection</strong> link or visit <code>/collections</code>.</li>
    <li>You'll see every collection as a row with item count and a chevron.</li>
    <li>Click a row to open the collection.</li>
</ol>

<h3>Inside a collection</h3>
<ul>
    <li><strong>Preview pane</strong> — cycle through the items inline with prev/next chevrons, or click any thumbnail in the strip below.</li>
    <li><strong>Media list</strong> — every item in the collection, click to open in collection context.</li>
    <li><strong>Details panel</strong> — counts per type, creation date.</li>
    <li><strong>Actions</strong>:
        <ul>
            <li><strong>Edit Collection</strong> (admin) — rename or change membership.</li>
            <li><strong>Download (.zip)</strong> — bundles every uploaded file in the collection into a single archive.</li>
            <li><strong>Add to My List / Remove from My List</strong> — saves the whole collection to your personal list.</li>
            <li><strong>Delete Collection</strong> (admin) — drops the collection. The media files stay.</li>
        </ul>
    </li>
</ul>

{{-- 5. MY LIST --}}
<h1>5. My&nbsp;List</h1>
<p>My&nbsp;List is your personal shelf. Use it to save items you want quick access to later.</p>

<h3>What it can hold</h3>
<ul>
    <li><strong>Saved media</strong> — individual photos, videos, books.</li>
    <li><strong>Saved collections</strong> — entire collections.</li>
</ul>

<h3>Adding items</h3>
<table>
    <tr><th>Where</th><th>How</th></tr>
    <tr><td>From any media's details page</td><td>Click <strong>Add to My List</strong> in the Actions card.</td></tr>
    <tr><td>From any collection's details page</td><td>Click <strong>Add to My List</strong> in the Actions card.</td></tr>
    <tr><td>In bulk</td><td>On the My&nbsp;List page, click <strong>+ Add to My List</strong>. The picker has two tabs (Media / Collections), filterable by name, with live counters. Tick what you want and click <strong>Save list</strong>.</td></tr>
</table>

<h3>Removing items</h3>
<p>Open the My&nbsp;List page and click the red trash icon on any row. Or untick it inside the bulk picker.</p>

{{-- 6. PROFILE --}}
<h1>6. Your Profile</h1>

<h3>Open it</h3>
<p>Click your name (top right) → <strong>Profile</strong>.</p>

<h3>What's on the page</h3>
<ul>
    <li><strong>Left card</strong> — read-only summary: avatar (initial), name, email, username, role, member-since date.</li>
    <li><strong>Right card — Change password</strong>:
        <ol>
            <li>Type your current password.</li>
            <li>Choose a new password (min. 8 characters).</li>
            <li>Re-type it in the confirmation field.</li>
            <li>Click <strong>Update password</strong>. A green banner confirms success.</li>
        </ol>
    </li>
</ul>

<div class="callout warn">
    <strong>Heads up.</strong> If the current password doesn't match, you'll see an inline error and the form stays open. No password gets changed.
</div>

<div class="page-break"></div>

{{-- 7. ADMIN MEDIA --}}
<h1>7. Admin: Managing Media</h1>

<h3>Status workflow</h3>
<table>
    <tr><th>Status</th><th>Who sees it</th><th>Used for</th></tr>
    <tr><td><span class="pill pill-amber">Draft</span></td><td>Admins only</td><td>Work-in-progress</td></tr>
    <tr><td><span class="pill pill-green">Published</span></td><td>Everyone</td><td>Live, visible</td></tr>
    <tr><td><span class="pill pill-slate">Archived</span></td><td>Admins only</td><td>Retired but kept</td></tr>
</table>

<h3>Adding media</h3>
<ol>
    <li>On the Media page, click <strong>+ Add Media</strong>.</li>
    <li>Fill in <strong>Title</strong>.</li>
    <li>Pick the <strong>Type</strong> — Photo, Video, or Book. The file input will only accept matching file types.</li>
    <li>Pick a <strong>Status</strong> — start with Draft if you're not ready to publish.</li>
    <li>Set an optional <strong>Date</strong> (the event date — separate from the upload date).</li>
    <li>Choose a <strong>Source</strong>:
        <ul>
            <li><strong>Upload file</strong> &mdash; max 50 MB, type-restricted (images for Photo, videos for Video, PDF/DOC/DOCX/XLS/XLSX for Book).</li>
            <li><strong>Use link</strong> &mdash; paste a direct URL to a hosted file.</li>
        </ul>
    </li>
    <li>(Books only) Add a <strong>Location</strong> — physical shelf reference.</li>
    <li>Upload a <strong>Thumbnail</strong> (optional, max 1 MB).</li>
    <li>Pick one or more <strong>Categories</strong> and <strong>Tags</strong>. Use the <strong>Manage</strong> shortcut to add new ones.</li>
    <li>Pick a <strong>Collection</strong> (optional) if this item belongs to one.</li>
    <li>Click <strong>Upload media</strong>. You'll be redirected to the new item's details page.</li>
</ol>

<h3>Editing or deleting media</h3>
<p>Open any media's details page → use the <strong>Actions</strong> card on the right. <strong>Delete</strong> shows a confirmation popup; the file and metadata are removed from disk and DB.</p>

<h3>Status tabs</h3>
<p>The Media page header has tabs: <strong>All · Draft · Published · Archived</strong> (admins only). Each shows a live count that respects whatever other filters you have active.</p>

<h3>Add Categories</h3>
<p>From the Add Media form, the <strong>Manage</strong> chip next to Categories opens the Categories page in a new tab. Categories used by any media cannot be deleted.</p>

{{-- 8. CATS + TAGS --}}
<h1>8. Admin: Categories &amp; Tags</h1>
<p>Both are similar: a name field, an Add button, then a table of existing entries with edit-in-place and delete.</p>
<table>
    <tr><th>Page</th><th>URL</th><th>Notes</th></tr>
    <tr><td>Categories</td><td><code>/categories</code></td><td>One name, used to group media. Cannot delete while in use.</td></tr>
    <tr><td>Tags</td><td><code>/tags</code></td><td>Hash-style labels. Cannot delete while in use.</td></tr>
</table>

{{-- 9. USERS --}}
<h1>9. Admin: Users</h1>

<h3>Adding a user</h3>
<ol>
    <li>From the sidebar, click <strong>Users</strong>.</li>
    <li>Click <strong>+ Add User</strong>.</li>
    <li>Fill in <strong>Name</strong>, <strong>Username</strong>, <strong>Email</strong>.</li>
    <li>Set the initial <strong>Password</strong> (min 8 chars) and confirm it.</li>
    <li>Pick a <strong>Role</strong>:
        <ul>
            <li><strong>Admin</strong> &mdash; full access.</li>
            <li><strong>User</strong> &mdash; browse + My&nbsp;List + profile only.</li>
        </ul>
    </li>
    <li>Click <strong>Create user</strong>.</li>
</ol>

<h3>Editing a user</h3>
<p>From the table, click <strong>Edit</strong>. You can leave the password fields blank to keep the existing password. Email and username can be changed but must remain unique.</p>

<h3>Deleting a user</h3>
<p>From the table, click <strong>Delete</strong> and confirm. <em>You cannot delete your own account.</em></p>

{{-- 10. SLIDESHOW --}}
<h1>10. Admin: Slider</h1>
<p>The home-page hero area pulls from the <strong>Slider</strong> page. Use this page to add or remove slides.</p>
<ol>
    <li>From the sidebar, click <strong>Slider</strong>.</li>
    <li>Type a <strong>Slide title</strong> and pick an image (max 2 MB).</li>
    <li>Click <strong>+ Add Slide</strong>. The new slide appears in the grid below.</li>
    <li>Click the red round button on any tile to delete that slide.</li>
</ol>

{{-- 11. TIPS --}}
<h1>11. Tips &amp; Troubleshooting</h1>

<table>
    <tr><th>Symptom</th><th>Fix</th></tr>
    <tr><td>Uploaded image shows a generic logo on the listing</td><td>The file extension didn't match the media type. Re-upload with a matching extension, or add a proper thumbnail.</td></tr>
    <tr><td>"Cannot delete — still assigned" when removing a category/tag</td><td>Reassign the media to a different category/tag first, then delete.</td></tr>
    <tr><td>Page numbers reset when toggling sort</td><td>By design — sort changes the ordering, so we drop back to page 1.</td></tr>
    <tr><td>Forgot which collection an item belongs to</td><td>Open the item; the Collection card on the right shows it.</td></tr>
    <tr><td>Drafts disappear when I switch from admin to a non-admin account</td><td>Non-admins never see drafts or archived rows. Switch back to your admin account.</td></tr>
    <tr><td>The "Download" button on a book is missing</td><td>That book was added with <strong>Use link</strong>, not uploaded. There's nothing to download locally.</td></tr>
</table>

<div class="footer-note">
    © {{ date('Y') }} Puncak Niaga Library Portal — User Manual
</div>

@endsection
