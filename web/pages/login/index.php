<?php

session_start();

$invalidEmail = isset($_SESSION["error"]) && isset($_SESSION["error"]["email"]);
$invalidPassword = isset($_SESSION["error"]) && isset($_SESSION["error"]["password"]);
$previousEmail = $_SESSION["previous"]["email"] ?? "";
?>

<div class="h-dvh bg-[url(/assets/building.jpg)] bg-blend-darken backdrop-brightness-75">
    <div class="w-3/4 h-full mx-auto py-12 bg-primary text-white shadow-2xl">
        <div class="flex justify-center items-center w-fit mx-auto mb-4 p-6 aspect-square rounded-full bg-white shadow-xl">
            <span class="h-fit text-8xl text-logo font-logo">N2N</span>
        </div>
        <h1 class="mb-12 text-center text-4xl font-bold drop-shadow-xl">N2N SOLUTIONS</h1>
        <form action="/login/handler" method="POST" class="w-full max-w-[400px] mx-auto">
            <div class="flex flex-col mb-8">
                <label for="email" class="font-bold">Email</label>
                <input type="email" id="email" name="email" required value="<?= $previousEmail ?>" data-error="<?= var_export($invalidEmail) ?>" class="login-input" />
                <?php if ($invalidEmail) : ?>
                    <span class="mt-1 text-red-400 text-sm font-bold">Email not found</span>
                <?php endif; ?>
            </div>
            <div class="flex flex-col mb-8">
                <label for="password" class="font-bold">Password</label>
                <input type="password" id="password" name="password" required data-error="<?= var_export($invalidPassword) ?>" class="login-input" />
                <?php if ($invalidPassword) : ?>
                    <span class="mt-1 text-red-400 text-sm font-bold">Incorrect Password</span>
                <?php endif; ?>
            </div>
            <div class="flex items-center justify-between">
                <label for="remember" class="flex items-center gap-2">
                    <div class="login-checkbox">
                        <input type="checkbox" id="remember" name="remember">
                        <i class="material-symbols-rounded">check</i>
                    </div>
                    <span>Remember me</span>
                </label>
                <button type="submit" class="login-submit">Login</button>
            </div>
        </form>
    </div>
</div>

<?php
unset($_SESSION["error"]);
unset($_SESSION["previous"]);
?>