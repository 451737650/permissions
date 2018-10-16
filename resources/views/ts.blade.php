<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>permission</title>
	</head>
	<body>
		@role('writer')
    I'm a writer!
@else
    I'm not a writer...
@endrole

@hasrole('writer')
    I'm a writer!
@else
    I'm not a writer...
@endhasrole

@hasanyrole(Role::all())
    I have one or more of these roles!
@else
    I have none of these roles...
@endhasanyrole

@hasallroles(Role::all())
    I have all of these roles!
@else
    I don't have all of these roles...
@endhasallroles

@can('Edit Post')
    I have permission to edit
@endcan
	</body>
</html>
