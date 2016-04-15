import java.sql.*;
import java.util.*;
import java.util.concurrent.*;
import java.util.logging.*;

public class SixDegrees {

	private int source, target;

	private Connection conn = null;
	private Statement st    = null;
	private ResultSet rs    = null;
	private String url      = "jdbc:mysql://mysql.chrismerlo.net:3306/album_credits";
	private String user     = "cmerlo";
	private String pw       = "sharkinfestedpiss";

	private ConcurrentLinkedQueue<Person> q;
	private HashMap<Integer,Boolean> used;
	private Person root;
	private Person end;

	private Logger lgr;

	public SixDegrees() {
		this( 1, 127 );  // Bruford --> Govan
	}

	public SixDegrees( int s, int t ) {
		boolean found;
		String name = null;

		source = s; target = t;

	    lgr = Logger.getLogger( SixDegrees.class.getName() );

		q = new ConcurrentLinkedQueue<>();
		used = new HashMap<>();
		end = null;

		try {
			Class.forName( "com.mysql.jdbc.Driver" ).newInstance();

			conn = DriverManager.getConnection( url, user, pw );
			st   = conn.createStatement();

			String query = "select first_name as f, last_name as l "
				+ "from people where id = " + source;
			rs = st.executeQuery( query );
			if( rs.next() )
				name = rs.getString( "f" ) + " " + rs.getString( "l" );

			root = new Person( null, source, name, 0, null, 0, null );
			q.add( root );

			found = fill_graph( root );
			if( found ) {
				// list all the albums these two played on together
			}

			while( ! found && ! q.isEmpty() ) {
				Person node = q.poll();  // remove the head
				found = fill_graph( node );
				if( found )
					break;
			}

			if( end == null )
				System.out.println( "No connection." );
			else
				System.out.println( end );

		} catch( SQLException ex ) {
			lgr.log( Level.SEVERE, ex.getMessage(), ex );
		} catch( InstantiationException ex ) {
			lgr.log( Level.SEVERE, ex.getMessage(), ex );
		} catch( IllegalAccessException ex ) {
			lgr.log( Level.SEVERE, ex.getMessage(), ex );
		} catch( ClassNotFoundException ex ) {
			lgr.log( Level.SEVERE, ex.getMessage(), ex );
		} finally {
		    try {
				if( rs != null )
				    rs.close();
				if( st != null )
				    st.close();
				if( conn != null )
				    conn.close();
		    } catch( SQLException ex ) {
				lgr.log( Level.WARNING, ex.getMessage(), ex );
			}
		}
	}

    private boolean fill_graph( Person node ) {
    	
    	Person p = null;

    	int p2id = 0;
    	String p2name = null;
    	int artist_id = 0;
    	String artist = null;
    	int album_id = 0;
    	String title = null;

    	ArrayList<String> queries = new ArrayList<>();

    	// album credit <--> album credit

		queries.add( "select p1.id as p1id, p1.first_name as p1f, p1.last_name as p1l, "
		    + "p2.id as p2id, p2.first_name as p2f, p2.last_name as p2l, "
		    + "aa.id as artist_id, aa.name as artist, a.id as album_id, a.name as title, "
		    + "c2.id as credit_id "
		    + "from album_artists as aa, albums as a, people as p1, people as p2, "
		    + "musician_album_credits as c1, musician_album_credits as c2 "
		    + "where c1.album = a.id "
		    + "and c2.album = c1.album "
		    + "and a.album_artist = aa.id "
		    + "and c1.musician = " + node.getId() + " "
		    + "and c1.musician = p1.id "
		    + "and c2.musician = p2.id "
		    + "and c1.musician != c2.musician "
		    + "group by p2.id, a.id "
		    + "order by a.release_date, p2.id" );

		// album credit <--> song credit
		queries.add( "select p1.id as p1id, p1.first_name as p1f, p1.last_name as p1l, "
	        + "p2.id as p2id, p2.first_name as p2f, p2.last_name as p2l, "
	        + "aa.id as artist_id, aa.name as artist, a.id as album_id, a.name as title, "
	        + "c2.id as credit_id "
	        + "from album_artists as aa, albums as a, people as p1, people as p2, "
	        + "musician_song_credits as c1, musician_song_credits as c2, songs as s1, songs as s2 "
	        + "where c1.song = s1.id and s1.album = a.id "
	        + "and c2.song = s2.id and s2.album = s1.album "
	        + "and a.album_artist = aa.id "
	        + "and c1.musician = " + node.getId() + " "
	        + "and c1.musician = p1.id "
	        + "and c2.musician = p2.id "
	        + "and c1.musician != c2.musician "
	        + "group by p2.id, a.id "
	        + "order by a.release_date, p2.id" );

		// song credit <--> album credit
		queries.add( "select p1.id as p1id, p1.first_name as p1f, p1.last_name as p1l, "
	        + "p2.id as p2id, p2.first_name as p2f, p2.last_name as p2l, "
	        + "aa.id as artist_id, aa.name as artist, a.id as album_id, a.name as title, "
	        + "c2.id as credit_id "
	        + "from album_artists as aa, albums as a, people as p1, people as p2, "
	        + "musician_song_credits as c1, musician_album_credits as c2, songs as s "
	        + "where c1.song = s.id and s.album = a.id "
	        + "and c2.album = s.album "
	        + "and a.album_artist = aa.id "
	        + "and c1.musician = " + node.getId() + " "
	        + "and c1.musician = p1.id "
	        + "and c2.musician = p2.id "
	        + "and c1.musician != c2.musician "
	        + "group by p2.id, a.id "
	        + "order by a.release_date, p2.id" );

		// song credit <--> song credit
		queries.add( "select p1.id as p1id, p1.first_name as p1f, p1.last_name as p1l, "
	        + "p2.id as p2id, p2.first_name as p2f, p2.last_name as p2l, "
	        + "aa.id as artist_id, aa.name as artist, a.id as album_id, a.name as title, "
	        + "c2.id as credit_id "
	        + "from album_artists as aa, albums as a, people as p1, people as p2, "
	        + "musician_album_credits as c1, musician_song_credits as c2, songs as s "
	        + "where c1.album = a.id "
	        + "and c2.song = s.id and s.album = c1.album "
	        + "and a.album_artist = aa.id "
	        + "and c1.musician = " + node.getId() + " "
	        + "and c1.musician = p1.id "
	        + "and c2.musician = p2.id "
	        + "and c1.musician != c2.musician "
	        + "group by p2.id, a.id "
	        + "order by a.release_date, p2.id" );

		for( String query : queries ) {
		    try {
			    ResultSet resultSet = st.executeQuery( query );

			    while( resultSet.next() ) {
				    p2id = resultSet.getInt( "p2id" );
				    p2name = resultSet.getString( "p2f" ) + " " + resultSet.getString( "p2l" );
				    artist_id = resultSet.getInt( "artist_id" );
				    artist = resultSet.getString( "artist" );
				    album_id = resultSet.getInt( "album_id" );
				    title = resultSet.getString( "title" );

				    // If we haven't searched based on this person yet
				    if( ! used.containsKey( p2id ) || ! used.get( p2id ) ) {
				    	used.put( p2id, true );
				    	p = new Person( node, p2id, p2name, artist_id, artist, album_id, title );
				    	node.setNext( p );
				    	q.add( p );
				    }

				    // Did we find the target?
				    if( p2id == target ) {
				    	end = p;
				    	return true;
				    }
				}
			} catch( SQLException ex ) {
				lgr.log( Level.SEVERE, ex.getMessage(), ex );
			}
		}

	    return false;
	}

	public static void main( String args[ ] ) {
		SixDegrees x;
		if( args.length != 2 )
			x = new SixDegrees();
		else
			x = new SixDegrees( Integer.parseInt( args[ 0 ] ), Integer.parseInt( args[ 1 ] ) );
	}
}
