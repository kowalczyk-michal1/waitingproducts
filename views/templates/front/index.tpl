<h3>Lista oczekujących</h3>

<div id="show_text">
{$show_text}
</div>

{if !$info}
<form action="" method="post" id="add_email_form" class="box">
		<fieldset>

			<div id="login_form_content">
				<p class="form-group">
					<label for="login_email">{l s='Adres email'}</label>
					<input type="email" class="form-control validate" id="email" name="email" data-validate="isEmail" />
				</p>
			
				<p class="submit">
					<button type="submit" id="SubmitWP" name="SubmitWP" class="button btn btn-default button-small"><span>{l s='Dodaj do listy oczekujących'}</span></button>
				</p>
			</div>
		</fieldset>
	</form>
{else}
{$info}
{/if}