@php
    $isEdit = isset($user) && $user;
@endphp

@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

@php
    // Reusable Tailwind class strings for floating-label inputs.
    $inputClass = 'peer w-full px-4 pt-4 pb-2 rounded-xl border-2 border-slate-200 focus:border-lib-sky focus:outline-none text-slate-800 bg-white placeholder-transparent transition-colors';
    $labelClass = 'absolute left-3 -top-2.5 px-1.5 bg-white text-xs font-semibold text-slate-500 peer-focus:text-lib-sky peer-focus:-top-2.5 peer-focus:left-3 peer-focus:text-xs peer-placeholder-shown:top-3.5 peer-placeholder-shown:left-4 peer-placeholder-shown:text-sm peer-placeholder-shown:text-slate-400 peer-placeholder-shown:font-normal transition-all pointer-events-none';
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    <div class="relative">
        <input id="name" type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required placeholder=" "
               class="{{ $inputClass }}">
        <label for="name" class="{{ $labelClass }}">Name</label>
    </div>
    <div class="relative">
        <input id="username" type="text" name="username" value="{{ old('username', $user->username ?? '') }}" required autocomplete="off" placeholder=" "
               class="{{ $inputClass }}">
        <label for="username" class="{{ $labelClass }}">Username</label>
    </div>
</div>

<div class="relative">
    <input id="email" type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required placeholder=" "
           class="{{ $inputClass }}">
    <label for="email" class="{{ $labelClass }}">Email</label>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    <div class="relative">
        <input id="password" type="password" name="password" autocomplete="new-password" {{ $isEdit ? '' : 'required' }} placeholder=" "
               class="{{ $inputClass }}">
        <label for="password" class="{{ $labelClass }}">
            Password @if($isEdit) <span class="font-normal text-slate-400">(leave blank to keep)</span> @endif
        </label>
    </div>
    <div class="relative">
        <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" {{ $isEdit ? '' : 'required' }} placeholder=" "
               class="{{ $inputClass }}">
        <label for="password_confirmation" class="{{ $labelClass }}">Confirm password</label>
    </div>
</div>

{{-- Role select uses a slightly different floating-label pattern --}}
<div class="relative">
    @php $current = old('role', $user->role ?? 'user'); @endphp
    <select id="role" name="role" required
            class="peer w-full px-4 pt-4 pb-2 rounded-xl border-2 border-slate-200 focus:border-lib-sky focus:outline-none text-slate-800 bg-white transition-colors appearance-none">
        <option value="user"  {{ $current === 'user'  ? 'selected' : '' }}>User</option>
        <option value="admin" {{ $current === 'admin' ? 'selected' : '' }}>Admin</option>
    </select>
    <label for="role" class="absolute left-3 -top-2.5 px-1.5 bg-white text-xs font-semibold text-slate-500 peer-focus:text-lib-sky pointer-events-none">Role</label>
    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </span>
</div>
