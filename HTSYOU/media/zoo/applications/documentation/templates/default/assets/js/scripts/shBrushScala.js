/*
 SyntaxHighlighter
 http://alexgorbatchev.com/

 SyntaxHighlighter is donationware. If you are using it, please donate.
 http://alexgorbatchev.com/wiki/SyntaxHighlighter:Donate

 @version
 2.1.382 (June 24 2010)

 @copyright
 Copyright (C) 2004-2009 Alex Gorbatchev.

 @license
 This file is part of SyntaxHighlighter.

 SyntaxHighlighter is free software: you can redistribute it and/or modify
 it under the terms of the GNU Lesser General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 SyntaxHighlighter is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with SyntaxHighlighter.  If not, see <http://www.gnu.org/copyleft/lesser.html>.
*/
SyntaxHighlighter.brushes.Scala=function(){this.regexList=[{regex:SyntaxHighlighter.regexLib.singleLineCComments,css:"comments"},{regex:SyntaxHighlighter.regexLib.multiLineCComments,css:"comments"},{regex:SyntaxHighlighter.regexLib.multiLineSingleQuotedString,css:"string"},{regex:SyntaxHighlighter.regexLib.multiLineDoubleQuotedString,css:"string"},{regex:SyntaxHighlighter.regexLib.singleQuotedString,css:"string"},{regex:/0x[a-f0-9]+|\d+(\.\d+)?/gi,css:"value"},{regex:RegExp(this.getKeywords("val sealed case def true trait implicit forSome import match object null finally super override try lazy for var catch throw type extends class while with new final yield abstract else do if return protected private this package false"),
"gm"),css:"keyword"},{regex:RegExp("[_:=><%#@]+","gm"),css:"keyword"}]};SyntaxHighlighter.brushes.Scala.prototype=new SyntaxHighlighter.Highlighter;SyntaxHighlighter.brushes.Scala.aliases=["scala"];
