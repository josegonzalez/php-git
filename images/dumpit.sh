
for a in $*
do
	echo "	\$img['$a']['name'] = \"$a\";"
	echo -n "	\$img['$a']['bin'] = " 
	hexdump -f dumper.hd $a | sed 's/U  //g' | sed 's/G/"/g' | sed 's/U/\\x/g'
	echo
done
