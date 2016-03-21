set USERPATH=%PATH%

set PATH=%PATH%;C:\Program Files (x86)\Graphviz2.38\bin

echo Update database
CMD /C vendor\bin\doctrine orm:schema-tool:update --force --dump-sql
echo Update documentation
CMD /C vendor\bin\phpdoc -d ./src,./bootstrap,./app,./database -t ./docs

SET PATH=%USERPATH%