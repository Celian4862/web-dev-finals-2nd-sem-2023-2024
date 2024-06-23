<?php
session_start();

$invalidEmail = isset($_SESSION["error"]) && isset($_SESSION["error"]["email"]);
$invalidPassword = isset($_SESSION["error"]) && isset($_SESSION["error"]["password"]);
$previousEmail = $_SESSION["previous"]["email"] ?? "";
?>

<div class="h-dvh bg-building bg-blend-darken backdrop-brightness-75">
	<div class="w-3/4 h-full mx-auto py-12 bg-primary text-white shadow-2xl">
		<div class="flex justify-center items-center w-fit mx-auto mb-4 p-6 aspect-square rounded-full bg-white shadow-xl">
			<span class="h-fit text-8xl text-logo font-logo">N2N</span>
		</div>
		<h1 class="mb-12 text-center text-4xl font-bold drop-shadow-xl">N2N SOLUTIONS</h1>
		<form action="/login/handler" method="POST" class="w-full max-w-[400px] mx-auto">
			<div class="flex flex-col mb-8">
				<label for="email" class="font-bold">Email</label>
				<input type="email" id="email" name="email" required value="<?= $previousEmail; ?>" data-error="<?= var_export($invalidEmail); ?>" class="
					p-2 rounded-md outline-none text-black ring-gray-300
					hover:ring focus:ring transition-colors shadow-md
					data-[error='true']:ring-red-400 data-[error='true']:ring
				" />
				<?php if ($invalidEmail) : ?>
					<span class="mt-1 text-red-400 text-sm font-bold">Email not found</span>
				<?php endif; ?>
			</div>
			<div class="flex flex-col mb-8">
				<label for="password" class="font-bold">Password</label>
				<input type="password" id="password" name="password" required data-error="<?= var_export($invalidPassword); ?>" class="
					p-2 rounded-md outline-none text-black ring-gray-300
					hover:ring focus:ring transition-colors shadow-md
					data-[error='true']:ring-red-400 data-[error='true']:ring
				" />
				<?php if ($invalidPassword) : ?>
					<span class="mt-1 text-red-400 text-sm font-bold">Incorrect Password</span>
				<?php endif; ?>
			</div>
			<div class="flex items-center justify-between">
				<div class="flex items-center">
					<input type="checkbox" id="remember" name="remember" class="
						peer relative appearance-none w-5 h-5 border rounded outline-none ring-gray-300 hover:ring focus:ring
						after:content-[''] after:absolute after:w-full after:h-full after:left-0 after:right-0 after:bg-no-repeat after:bg-center
						after:bg-[length:40px] checked:after:bg-[url('/assets/check.svg')]
					">
					<label for="remember" class="ml-2">Remember me</label>
				</div>
				<button type="submit" class="px-4 py-2 rounded-md outline-none bg-green-500 ring-green-500 font-bold hover:bg-green-600 hover:ring focus:bg-green-600 focus:ring transition-colors shadow-md">Login</button>
			</div>
		</form>
	</div>
</div>

<?php
unset($_SESSION["error"]);
unset($_SESSION["previous"]);
?>