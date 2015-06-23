// JavaScript Document
//=====================================
// 功能 PHP语法加亮函数
// author:ice_berg16(寻梦的稻草人)
// lastModified:2005-6-29
// copyright(c)2005 ice_berg16@163.com
//=====================================
function highlight_string( str )
{
 //add a new prototype function to array
 Array.prototype.exist = function(v)
 {
  for(k=0;k<this.length;k++)
  {
   if(this[k].toLowerCase() == v.toLowerCase())
    return true;
  }
  return false;
 }
 
 //base variable
 var operator = "><=,()[].+-*/!&|^~?{};:";
 var keyword  = ['and','or','__FILE__','exception','__LINE__','array','as','break','case','class','const',
     'continue','declare','default','die','do','echo','else','elseif','empty','enddeclare','endfor',
     'endforeach','endif','endswitch','endwhile','eval','exit','extends','for','foreach','function',
     'global','if','include','include_once','isset','list','new','old_function','print','require',
     'require_once','return', 'static','switch','unset','var','while','__FUNCTION__','__CLASS__',
     '__METHOD__','true','false','null'];

 var inString = false; 
 var inSLComment = false; //single line comment
 var inMLComment = false; //multiline comment
 var delimiter = null;
 var startPos = null;
 var word  = "";
 var res = "";

 //start to format
 for(i=0;i<str.length;i++)
 {
  if( inString ) //we are in string
  {
   //the word cache will be clear
   if(word != "") //we check the word cache if it the key word
   {
    if( keyword.exist(word) ) //its php reversed keyword,rend color
     res+= rendColor(word, 'keyword');
    else
     res+= word;
    word = "";
   }
   //alert('inString,pos is '+ i+',char is '+c );
   fromPos = startPos+1;
   while(1)
   {
    //we find the end of current string
    p = str.indexOf( delimiter, fromPos );
    
    //we got the end of the code
    if( p == -1 )
    {
     curstr = str.substr( startPos );
     res += rendColor( curstr, 'string' );
     i = str.length;
     break;
    }
    if( p != -1 && str.charAt(p-1) != "\\" )
    {
     i = p+1;
     curstr  = str.substring(startPos, i ); //get the current string
     res += rendColor( curstr, 'string' ); //rend color for it and add it to the result
     inString = false; //we have go out of the string
     startPos = null;
     break;
    }
    else
    {
     fromPos = p+1;
    }
   }
  }
  if( inSLComment ) //we are in Single line comment
  {
   if(word != "") //we check the word cache if it the key word
   {
    if( keyword.exist(word) ) //its php reversed keyword,rend color
     res+= rendColor(word, 'keyword');
    else
     res+= word;
    word = "";
   }
   //alert('inSLComment,pos is '+ i+',char is '+c );
   p = str.indexOf( "\n", i );
   if( p != -1 ) //we find the end of line
   {
    i = p;
    curstr = str.substring( startPos, p );
    res += rendColor( curstr, 'comment' );
    startPos = null;
    inSLComment = false;
   }
   else
   {
    curstr = str.substr( startPos );
    res += rendColor( curstr, 'comment' );
    i = str.length;
   }
  }
  if( inMLComment ) //we are in multiline comment
  {
   if(word != "") //we check the word cache if it the key word
   {
    if( keyword.exist(word) ) //its php reversed keyword,rend color
     res+= rendColor(word, 'keyword');
    else
     res+= word;
    word = "";
   }
   //alert('inMLComment,pos is '+ i+',char is '+c );
   p = str.indexOf( "*/", startPos+2 );
   if( p != -1 ) //we find the end of line
   {
    i = p+2;
    curstr = str.substring(startPos, i );
    res += rendColor( curstr, 'comment' );
    startPos = null;
    inMLComment = false;
   }
   else
   {
    curstr = str.substr( startPos );
    res += rendColor( curstr, 'comment' );
    i = str.length; 
   }
  }

  var c  = str.charAt(i); //current char
  var nc = str.charAt(i+1);//next char

  switch( c )
  {
   case '/':
    if( nc == '*' ) // we go into the multiline comment
    {
     inMLComment = true; 
     startPos = i;
    }
    if( nc == "/" ) //we are in single line comment
    {
     inSLComment = true;
     startPos = i;
    }
    //alert('we are in switch,pos is '+i+', and char is'+ c);
    break;

   case '#':
    inSLComment = true; //we go into the single line comment
    startPos = i;
    break;

   case '"':
    inString = true;
    delimiter = '"';
    startPos = i;
    break;

   case "'":
    inString = true;
    delimiter = "'";
    startPos = i;
    break;

   default:
    if( /[\w$]/.test(c) )  //the keyword only contains continuous common char
    {
     word += c;   //cache the current char 
    }
    else
    {
     if(word != "") //we check the word cache if it the key word
     {
      if( keyword.exist(word) ) //its php reversed keyword,rend color
       res+= rendColor(word, 'keyword');
      else
       res+= word;
      word = "";
     }
     //now the current char is not common char, we process it 
     if( operator.indexOf(c) != -1 ) // the char is a operator
      res += rendColor(c, 'operator' );
     else
      res += c;
    } 
    break;
  }
 }
 $t = "&nbsp;&nbsp;&nbsp;&nbsp;";
 $b = "&nbsp;";
 res = res.replace(/^( +)/g, function($1){c = $1.length;str="";while(--c>=0)str+=$b;return str});
 res = res.replace(/(\t| ){2,}/g, function($0){c=$0.length;str="";while(--c>=0){if($0.charAt(c)=='\t')str+=$t;else str+=$b;}return str;});
 res = res.replace(/\t/g,$t);
 res = res.replace(/\n/g, "\n</li><li>"); 
 res = '<ol style=" list-style-type:none;"><li>' + res + '</li></ol>';
 //alert(res);
 return res;
}

//对字符串中的HTML代码编码
function HTMLEncode( str )
{
 str = str.replace(/&/g, '&amp;');
 str = str.replace(/</g, '&lt;');
 str = str.replace(/>/g, '&gt;');
 return str;
}

//根据字符串所属类型渲染不同的着色
function rendColor( str, type )
{
 var commentColor = "#FF8000";
 var stringColor  = "#DD0000";
 var operatorColor= "#007700";
 var keywordColor = "#007700";
 var commonColor  = "#0000BB";
 var useColor  = null;
 str = HTMLEncode( str );

 
 //we will rend what color?
 switch( type )
 {
  case 'comment':
   useColor  = commentColor;
   break;
  case 'string':
   useColor = stringColor;
   break;
  case 'operator':
   useColor  = operatorColor;
   break;
  case 'keyword':
   useColor  = keywordColor;
   break;
  default:
   useColor  = commonColor;
   break;   
 }
 if( str.indexOf("\n") != -1 ) //there are more than one line
 {
  arr = str.split("\n");
  for(j=0;j<arr.length;j++)
  {
   arr[j] = "<span style='color:"+ useColor +"'>"+ arr[j] + "</span>";
  }
  return arr.join("\n");
 }
 else
 {
  str = "<span style='color:"+ useColor +"'>"+ str + "</span>";
  return str;
 }
}