if [ -z $1 ] || [ $1 = 'build' ] 
    then
        VERSAO="build"
    else
        VERSAO="$1"
fi

if [ $1 = 'get' ] 
    then
        ALL_VERSION=$(sed -n "s/\(\"versao.major\":\)\([0-9]*\)\(,\)/\2/p" vrcotacao.json | xargs)        
        ALL_VERSION+='.'$(sed -n "s/\(\"versao.minor\":\)\([0-9]*\)\(,\)/\2/p" vrcotacao.json | xargs)        
        ALL_VERSION+='.'$(sed -n "s/\(\"versao.release\":\)\([0-9]*\)\(,\)/\2/p" vrcotacao.json | xargs)        

        if [ $(sed -n "s/\(\"versao.build\":\)\([0-9]*\)\(,\)/\2/p" vrcotacao.json | xargs) != '0' ]
            then
                ALL_VERSION+='-'$(sed -n "s/\(\"versao.build\":\)\([0-9]*\)\(,\)/\2/p" vrcotacao.json | xargs)        
        fi    

        if [ $(sed -n "s/\(\"versao.beta\":\)\([0-9]*\)\(,\)/\2/p" vrcotacao.json | xargs) != '0' ]
            then
                ALL_VERSION+='b'$(sed -n "s/\(\"versao.beta\":\)\([0-9]*\)\(,\)/\2/p" vrcotacao.json | xargs)        
        fi    
        
        echo "$ALL_VERSION"
elif [ $1 = 'build' ] 
    then
        OLD_VERSION=$(sed -n "s/\(\"versao.$VERSAO\":\)\([0-9]*\)\(,\)/\2/p" vrcotacao.json | xargs)
        NEW_VERSION=$(($OLD_VERSION+1))
        DATA_ATUAL=$(date +%d\\/%m\\/%Y)
        sed -i "s/\(\"versao.$VERSAO\":\)\([0-9]*\)\(,\)/\1$NEW_VERSION\3/g" vrcotacao.json
        sed -i "s/\(\"versao.beta\":\)\([0-9]*\)\(,\)/\10\3/g" vrcotacao.json
        sed -i "s/\(\"data\":\)\(.*\)/\1\"$DATA_ATUAL\"/g" vrcotacao.json

elif [ $1 = 'release' ] 
    then
        OLD_VERSION=$(sed -n "s/\(\"versao.$VERSAO\":\)\([0-9]*\)\(,\)/\2/p" vrcotacao.json | xargs)
        NEW_VERSION=$(($OLD_VERSION+1))
        DATA_ATUAL=$(date +%d\\/%m\\/%Y)
        sed -i "s/\(\"versao.$VERSAO\":\)\([0-9]*\)\(,\)/\1$NEW_VERSION\3/g" vrcotacao.json
        sed -i "s/\(\"versao.build\":\)\([0-9]*\)\(,\)/\10\3/g" vrcotacao.json
        sed -i "s/\(\"versao.beta\":\)\([0-9]*\)\(,\)/\10\3/g" vrcotacao.json
        sed -i "s/\(\"data\":\)\(.*\)/\1\"$DATA_ATUAL\"/g" vrcotacao.json

elif [ $1 = 'minor' ] 
    then
        OLD_VERSION=$(sed -n "s/\(\"versao.$VERSAO\":\)\([0-9]*\)\(,\)/\2/p" vrcotacao.json | xargs)
        NEW_VERSION=$(($OLD_VERSION+1))
        DATA_ATUAL=$(date +%d\\/%m\\/%Y)
        sed -i "s/\(\"versao.$VERSAO\":\)\([0-9]*\)\(,\)/\1$NEW_VERSION\3/g" vrcotacao.json
        sed -i "s/\(\"versao.release\":\)\([0-9]*\)\(,\)/\10\3/g" vrcotacao.json
        sed -i "s/\(\"versao.build\":\)\([0-9]*\)\(,\)/\10\3/g" vrcotacao.json
        sed -i "s/\(\"versao.beta\":\)\([0-9]*\)\(,\)/\10\3/g" vrcotacao.json
        sed -i "s/\(\"data\":\)\(.*\)/\1\"$DATA_ATUAL\"/g" vrcotacao.json

elif [ $1 = 'major' ] 
    then
        OLD_VERSION=$(sed -n "s/\(\"versao.$VERSAO\":\)\([0-9]*\)\(,\)/\2/p" vrcotacao.json | xargs)
        NEW_VERSION=$(($OLD_VERSION+1))
        DATA_ATUAL=$(date +%d\\/%m\\/%Y)
        sed -i "s/\(\"versao.$VERSAO\":\)\([0-9]*\)\(,\)/\1$NEW_VERSION\3/g" vrcotacao.json
        sed -i "s/\(\"versao.minor\":\)\([0-9]*\)\(,\)/\10\3/g" vrcotacao.json
        sed -i "s/\(\"versao.release\":\)\([0-9]*\)\(,\)/\10\3/g" vrcotacao.json
        sed -i "s/\(\"versao.build\":\)\([0-9]*\)\(,\)/\10\3/g" vrcotacao.json
        sed -i "s/\(\"versao.beta\":\)\([0-9]*\)\(,\)/\10\3/g" vrcotacao.json
        sed -i "s/\(\"data\":\)\(.*\)/\1\"$DATA_ATUAL\"/g" vrcotacao.json

    else
        OLD_VERSION=$(sed -n "s/\(\"versao.$VERSAO\":\)\([0-9]*\)\(,\)/\2/p" vrcotacao.json | xargs)
        NEW_VERSION=$(($OLD_VERSION+1))
        DATA_ATUAL=$(date +%d\\/%m\\/%Y)
        sed -i "s/\(\"versao.$VERSAO\":\)\([0-9]*\)\(,\)/\1$NEW_VERSION\3/g" vrcotacao.json
        sed -i "s/\(\"data\":\)\(.*\)/\1\"$DATA_ATUAL\"/g" vrcotacao.json
fi

