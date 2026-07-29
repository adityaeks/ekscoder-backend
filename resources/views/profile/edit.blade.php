<x-app-layout title="Profile Settings" breadcrumb="Manage your account profile and security">
    <div style="max-width:860px;" class="space-y-6">
        
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Profile Information</div>
                    <div class="card-subtitle">Update your account's profile information and email address.</div>
                </div>
            </div>
            <div class="card-body">
                <div style="max-width:540px;">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Update Password</div>
                    <div class="card-subtitle">Ensure your account is using a long, random password to stay secure.</div>
                </div>
            </div>
            <div class="card-body">
                <div style="max-width:540px;">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>

        <div class="card" style="border-color: rgba(244,63,94,0.2);">
            <div class="card-header" style="border-bottom-color: rgba(244,63,94,0.15);">
                <div>
                    <div class="card-title" style="color:var(--rose);">Delete Account</div>
                    <div class="card-subtitle">Once your account is deleted, all of its resources and data will be permanently deleted.</div>
                </div>
            </div>
            <div class="card-body">
                <div style="max-width:540px;">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
