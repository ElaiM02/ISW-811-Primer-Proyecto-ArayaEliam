<x-layout>
    <form method="POST" action="{{ route('profile.update') }}" class="max-w-md mx-auto mt-10 space-y-4">
        @csrf
        @method('PATCH')

        <h1 class="text-2xl font-bold">Edit your account</h1>
        <p class="text-muted-foreground text-sm">Update your profile details.</p>

        @if (session('success'))
            <p class="text-primary text-sm">{{ session('success') }}</p>
        @endif

        <x-form.field label="Name" name="name" :value="$user->name" required />
        <x-form.field label="Email" name="email" type="email" :value="$user->email" required />
        <x-form.field label="New password" name="password" type="password"
                      placeholder="Leave blank to keep current password" />

        <button type="submit" class="btn w-full">Update account</button>
    </form>
</x-layout>
